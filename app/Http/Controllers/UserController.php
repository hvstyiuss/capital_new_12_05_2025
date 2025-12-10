<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Actions\User\ListUsersAction;
use App\Actions\User\ShowUserAction;
use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\DeleteUserAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $perPage = $request->get('per_page', 20);
        $users = app(ListUsersAction::class)->execute($request->all(), (int) $perPage);

        return new UserCollection($users);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user = app(ShowUserAction::class)->execute($user);

        return new UserResource($user);
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validated();
        $dto = new \App\DTOs\User\CreateUserDTO(
            ppr: $validated['ppr'],
            name: $validated['name'],
            password: $validated['password'],
            email: $validated['email'] ?? null,
            image: $validated['image'] ?? null,
            isActive: $validated['is_active'] ?? null
        );
        
        $user = app(CreateUserAction::class)->execute($dto);
        $user->load(['userInfo', 'entites', 'roles']);
        
        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validated = $request->validated();
        $dto = new \App\DTOs\User\UpdateUserDTO(
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
            image: $validated['image'] ?? null,
            isActive: $validated['is_active'] ?? null,
            password: $validated['password'] ?? null
        );
        
        $user = app(UpdateUserAction::class)->execute($user, $dto);
        $user->load(['userInfo', 'entites', 'roles']);
        
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        app(DeleteUserAction::class)->execute($user);
        
        return response()->noContent();
    }
}




