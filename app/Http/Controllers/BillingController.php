<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Laravel\Cashier\Checkout;

class BillingController extends Controller
{
    /**
     * Show the available plans and the user's current subscription status.
     */
    public function index(Request $request): View
    {
        return view('billing.index', [
            'plans' => config('billing.plans'),
            'subscription' => $request->user()->subscription('default'),
        ]);
    }

    /**
     * Begin a Stripe Checkout session for the chosen plan.
     */
    public function checkout(Request $request): Checkout|RedirectResponse
    {
        if ($request->user()->subscribed('default')) {
            return redirect()->route('billing.portal');
        }

        $validated = $request->validate([
            'plan' => ['required', 'string', Rule::in(array_keys(config('billing.plans')))],
        ]);

        $priceId = config("billing.plans.{$validated['plan']}.stripe_price");

        if (! $priceId) {
            return redirect()
                ->route('billing.index')
                ->with('error', __('Billing is not configured yet. Please try again later.'));
        }

        return $request->user()
            ->newSubscription('default', $priceId)
            ->trialDays((int) config('billing.trial_days'))
            ->checkout([
                'success_url' => route('billing.index').'?checkout=success',
                'cancel_url' => route('billing.index').'?checkout=cancelled',
            ]);
    }

    /**
     * Redirect the user to Stripe's hosted billing portal.
     */
    public function portal(Request $request): RedirectResponse
    {
        $request->user()->createOrGetStripeCustomer();

        return $request->user()->redirectToBillingPortal(route('dashboard'));
    }
}
