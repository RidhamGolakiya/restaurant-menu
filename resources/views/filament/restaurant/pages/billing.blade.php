<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700 flex flex-col justify-between
                {{ $currentRestaurant && $currentRestaurant->plan_id === $plan->id ? 'ring-2 ring-primary-500' : '' }}">
                
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                    <div class="mt-4 flex items-baseline text-gray-900 dark:text-white">
                        <span class="text-3xl font-extrabold tracking-tight">${{ number_format($plan->price, 2) }}</span>
                        <span class="ml-1 text-xl font-semibold text-gray-500 dark:text-gray-400">/{{ $plan->frequency }}</span>
                    </div>
                    
                    @if($currentRestaurant && $currentRestaurant->plan_id === $plan->id)
                        <div class="mt-2 text-sm font-medium text-primary-600 dark:text-primary-400">
                            Current Plan
                        </div>
                    @endif

                    <ul role="list" class="mt-6 space-y-4">
                        @if($plan->features)
                            @foreach($plan->features as $featureItem)
                                <li class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <!-- Heroicon name: outline/check -->
                                        <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700 dark:text-gray-300">{{ $featureItem['feature'] ?? $featureItem }}</p>
                                </li>
                            @endforeach
                        @else
                            <li class="text-gray-500 italic">No specific features listed.</li>
                        @endif
                    </ul>
                </div>

                <div class="mt-8">
                     @if($currentRestaurant && $currentRestaurant->plan_id === $plan->id)
                        <button disabled class="w-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 py-2 px-4 rounded-lg cursor-not-allowed border border-gray-200 dark:border-gray-600">
                            Current Plan
                        </button>
                     @elseif($pendingRequest && $pendingRequest->plan_id === $plan->id)
                        <button disabled class="w-full bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-200 py-2 px-4 rounded-lg cursor-not-allowed border border-yellow-200 dark:border-yellow-700">
                            Switch Pending Approval
                        </button>
                     @elseif($pendingRequest)
                        <button disabled class="w-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 py-2 px-4 rounded-lg cursor-not-allowed border border-gray-200 dark:border-gray-600" title="You have another pending request">
                            Switch Plan
                        </button>
                     @else
                        <button wire:click="requestPlan({{ $plan->id }})" 
                                wire:loading.attr="disabled"
                                class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <span wire:loading.remove wire:target="requestPlan({{ $plan->id }})">Request Switch</span>
                            <span wire:loading wire:target="requestPlan({{ $plan->id }})">Processing...</span>
                        </button>
                     @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
