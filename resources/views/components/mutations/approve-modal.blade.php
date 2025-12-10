@props([
    'mutationId',
    'mutationType',
    'isFinalValidation' => false,
    'approveRoute'
])

<!-- Flowbite Modal - No fade animation -->
<div id="approveModal{{ $mutationId }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" style="display: none; transition: none !important; animation: none !important;">
    <div class="relative p-4 w-full max-w-2xl max-h-full" style="transition: none !important; animation: none !important;">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800" style="transition: none !important; animation: none !important;">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 bg-gradient-to-r from-green-600 to-green-800">
                <h3 class="text-lg font-semibold text-white">
                    {{ $isFinalValidation ? 'Valider la mutation' : 'Approuver la mutation' }}
                </h3>
                <button type="button" class="text-white bg-transparent hover:bg-green-800 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600" data-modal-hide="approveModal{{ $mutationId }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fermer</span>
                </button>
            </div>
            <!-- Modal body -->
            <form method="POST" action="{{ $approveRoute }}">
                @csrf
                <div class="p-4 md:p-5 space-y-4">
                    @if($isFinalValidation)
                        <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                            Êtes-vous sûr de vouloir valider cette mutation ? Veuillez définir la date de début d'affectation.
                        </p>
                        <div>
                            <label for="date_debut_affectation{{ $mutationId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Date de début d'affectation <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="date_debut_affectation{{ $mutationId }}" 
                                   name="date_debut_affectation" 
                                   min="{{ date('Y-m-d') }}" 
                                   required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">La date doit être aujourd'hui ou une date future</p>
                        </div>
                    @elseif($mutationType === 'externe')
                        <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                            Êtes-vous sûr de vouloir approuver cette mutation ? Elle sera envoyée à la direction de destination pour validation.
                        </p>
                    @else
                        <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                            Êtes-vous sûr de vouloir approuver cette mutation ? Veuillez définir la date de début d'affectation.
                        </p>
                        <div>
                            <label for="date_debut_affectation{{ $mutationId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Date de début d'affectation <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="date_debut_affectation{{ $mutationId }}" 
                                   name="date_debut_affectation" 
                                   min="{{ date('Y-m-d') }}" 
                                   required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">La date doit être aujourd'hui ou une date future</p>
                        </div>
                    @endif
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        {{ $isFinalValidation ? 'Valider' : 'Approuver' }}
                    </button>
                    <button type="button" data-modal-hide="approveModal{{ $mutationId }}" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-green-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
