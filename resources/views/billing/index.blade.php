<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">{{ __('Billing') }}</h2>
    </x-slot>

    <div class="container">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if (request()->query('checkout') === 'success')
            <div class="alert alert-success">
                {{ __('Thanks for subscribing! Your ad-free access activates as soon as Stripe confirms the payment.') }}
            </div>
        @elseif (request()->query('checkout') === 'cancelled')
            <div class="alert alert-warning">
                {{ __('Checkout was cancelled. You have not been charged.') }}
            </div>
        @endif

        @if ($subscription && $subscription->valid())
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5">{{ __('Your subscription') }}</h3>

                    <p class="text-muted mb-1">
                        {{ __('Status:') }}
                        <span class="badge bg-success">{{ ucfirst($subscription->stripe_status) }}</span>
                    </p>

                    @if ($subscription->onTrial())
                        <p class="text-muted mb-1">
                            {{ __('Trial ends :date.', ['date' => $subscription->trial_ends_at->toFormattedDateString()]) }}
                        </p>
                    @endif

                    @if ($subscription->onGracePeriod())
                        <p class="text-muted mb-1">
                            {{ __('Your subscription is cancelled but access continues until :date.', ['date' => $subscription->ends_at->toFormattedDateString()]) }}
                        </p>
                    @endif

                    <a href="{{ route('billing.portal') }}" class="btn btn-primary mt-2">
                        {{ __('Manage subscription') }}
                    </a>
                </div>
            </div>
        @else
            <p class="text-muted">
                {{ __('Choose a plan to remove ads and support independent writing.') }}
            </p>

            <div class="row">
                @foreach ($plans as $key => $plan)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h3 class="h5">{{ $plan['name'] }}</h3>

                                <p class="display-6 fw-bold mb-2">
                                    {{ strtoupper(config('billing.currency')) }} {{ number_format($plan['price'] / 100, 2) }}
                                    <small class="fs-6 text-muted fw-normal">/ {{ $plan['interval'] }}</small>
                                </p>

                                <p class="text-muted">{{ $plan['description'] }}</p>

                                <p class="text-success small">
                                    {{ __('Includes a :days-day free trial.', ['days' => config('billing.trial_days')]) }}
                                </p>

                                @if ($plan['stripe_price'])
                                    <form method="POST" action="{{ route('billing.checkout') }}" class="mt-auto">
                                        @csrf
                                        <input type="hidden" name="plan" value="{{ $key }}">
                                        <button type="submit" class="btn btn-primary w-100">
                                            {{ __('Subscribe') }}
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-secondary w-100 mt-auto" disabled>
                                        {{ __('Coming soon') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
