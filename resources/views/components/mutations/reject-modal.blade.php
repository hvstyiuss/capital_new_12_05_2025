@props([
    'mutationId',
    'rejectRoute'
])

<!-- Flowbite Modal - No fade animation -->
<div id="rejectModal{{ $mutationId }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" style="display: none; transition: none !important; animation: none !important;">
    <div class="relative p-4 w-full max-w-2xl max-h-full" style="transition: none !important; animation: none !important;">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800" style="transition: none !important; animation: none !important;">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 bg-gradient-to-r from-red-600 to-red-800">
                <h3 class="text-lg font-semibold text-white">
                    Rejeter la réception
                </h3>
                <button type="button" class="text-white bg-transparent hover:bg-red-800 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600" data-modal-hide="rejectModal{{ $mutationId }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fermer</span>
                </button>
            </div>
            <!-- Modal body -->
            <form method="POST" action="{{ $rejectRoute }}">
                @csrf
                <div class="p-4 md:p-5 space-y-4">
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        Êtes-vous sûr de vouloir rejeter la réception de cette mutation ?
                    </p>
                    <div>
                        <label for="rejection_reason_super_rh{{ $mutationId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Raison du rejet <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason_super_rh{{ $mutationId }}" 
                                  name="rejection_reason_super_rh" 
                                  rows="4" 
                                  minlength="10" 
                                  required
                                  placeholder="Veuillez expliquer la raison du rejet (minimum 10 caractères)"
                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"></textarea>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Minimum 10 caractères</p>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        Rejeter
                    </button>
                    <button type="button" data-modal-hide="rejectModal{{ $mutationId }}" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
