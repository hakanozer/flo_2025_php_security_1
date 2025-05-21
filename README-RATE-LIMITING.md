# Rate Limiting Implementation

This document describes the rate limiting implementation in the project.

## Overview

Rate limiting has been implemented to protect the API from abuse and ensure fair usage of resources. Different endpoints have different rate limits based on their sensitivity and expected usage patterns.

## Rate Limiters

The following rate limiters have been defined in `app/Providers/RouteServiceProvider.php`:

1. **Default API Rate Limiter**: 60 requests per minute per user/IP
   - Applied to all API routes by default

2. **Auth Rate Limiter**: 10 requests per minute per IP
   - Applied to authentication endpoints (/register, /login)
   - Uses IP address for identification to prevent brute force attacks

3. **Notes API Rate Limiter**: 30 requests per minute per user/IP
   - Applied to all notes endpoints
   - Uses user ID for authenticated users, IP address for unauthenticated users

4. **Products API Rate Limiter**: 30 requests per minute per user/IP
   - Applied to all products endpoints
   - Uses user ID for authenticated users, IP address for unauthenticated users

## Implementation Details

1. Rate limiters are defined in `app/Providers/RouteServiceProvider.php`
2. The throttle middleware is registered in `bootstrap/app.php`
3. Rate limiters are applied to routes in `routes/api.php`

## Response Headers

When rate limiting is applied, the following headers are included in the response:

- `X-RateLimit-Limit`: The maximum number of requests allowed per time window
- `X-RateLimit-Remaining`: The number of requests remaining in the current time window
- `X-RateLimit-Reset`: The time at which the rate limit will reset (Unix timestamp)

When a rate limit is exceeded, a 429 (Too Many Requests) response is returned.

## Customization

To modify the rate limits, update the corresponding rate limiter in `app/Providers/RouteServiceProvider.php`.

For example, to change the auth rate limit from 10 to 5 requests per minute:

```php
RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```
