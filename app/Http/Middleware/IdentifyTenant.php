<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Routes that should bypass tenant identification
     * FIXED: Login route was commented out!
     */
    protected $except = [
        '/',           // Login page
        'login',       // MUST BE EXCLUDED
        'livewire/*',  // ADDED: Livewire requests
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Skip tenant identification for excluded routes
        if ($this->shouldBypass($request)) {
            Log::info('Bypassing tenant check for route: ' . $request->path());
            return $next($request);
        }

        $tenantName = null;

        // Method 1: From authenticated user
        if (auth()->check() && auth()->user()->tenant) {
            $tenantName = auth()->user()->tenant->name;
            Log::info('Tenant from auth user: ' . $tenantName);
        }

        // Method 2: From session
        if (!$tenantName && session()->has('tenant_name')) {
            $tenantName = session('tenant_name');
            Log::info('Tenant from session: ' . $tenantName);
        }

        // Method 3: From header (for API requests)
        if (!$tenantName) {
            $tenantName = $request->header('X-Tenant-Name');
            if ($tenantName) {
                Log::info('Tenant from header: ' . $tenantName);
            }
        }

        // If we found a tenant name, look it up in database
        if ($tenantName) {
            $tenant = Tenant::where('name', $tenantName)
                ->where('is_active', true)
                ->first();

            if ($tenant) {
                // Set tenant in service container
                app()->instance('tenant', $tenant);
                app()->instance('tenant.id', $tenant->id);

                // Store in session for next request
                session(['tenant_name' => $tenant->name]);

                // Optional: Set tenant-specific config
                config(['app.tenant' => $tenant->toArray()]);

                Log::info('Tenant context set successfully', [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->name
                ]);

                return $next($request);
            } else {
                Log::warning('Tenant not found: ' . $tenantName);
            }
        }

        // Tenant not identified
        Log::warning('Tenant not identified', [
            'path' => $request->path(),
            'authenticated' => auth()->check(),
            'has_session' => session()->has('tenant_name')
        ]);

        // Handle based on request type
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Tenant not identified',
                'message' => 'Please provide a valid tenant identifier'
            ], 403);
        }

        // If user is authenticated but no tenant
        if (auth()->check()) {
            Auth::logout(); // Log them out since they have no tenant
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is not associated with any organization.']);
        }

        // Not authenticated - redirect to login
        return redirect()->route('login')
            ->withErrors(['tenant' => 'Please login to continue']);
    }

    protected function shouldBypass(Request $request): bool
    {
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
