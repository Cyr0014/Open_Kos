Production deployment checklist

1. Environment
- Set APP_ENV=production
- Set APP_DEBUG=false
- Set APP_KEY to a real generated value
- Set APP_URL=https://your-domain.com
- Set TRUSTED_PROXIES to your actual proxy IPs or '*'

2. Web server / reverse proxy
- Make sure TLS terminates at the reverse proxy or load balancer
- Force HTTPS redirects at the edge
- Ensure proxy forwards X-Forwarded-Proto / X-Forwarded-For properly
- Block direct raw access to PHP app ports in production

3. Security
- Keep session cookies secure and HttpOnly
- Keep same_site=lax for the app
- Verify HTTPS-only cookies
- Use strong admin passwords and MFA for owners/admins
- Review access to payment gateway secrets and SMTP credentials

4. Auth & abuse protection
- Fortify login throttling already exists in app/Providers/FortifyServiceProvider.php
- Keep webhook route protected by throttle:payment-webhook
- Review logs for repeated login / payment errors

5. Database
- Run migrations in CI and on deploy
- Lock down DB privileges to the app user only
- Disable public database exposure
- Back up your DB and maintain retention

6. CI/CD
- Run:
  - composer install
  - npm ci
  - php artisan migrate --force
  - php artisan test
  - npm run lint:check
  - npm run types:check
  - npm run build
- Fail deployment if any step fails

7. Monitoring
- Enable Laravel logging to stdout or a central collector
- Monitor:
  - failed login attempts
  - invalid payment webhook attempts
  - failed payment processing
  - database errors / deadlocks
  - app exceptions

8. Release validation
- Test login flow
- Test password reset
- Test 2FA challenge
- Test payment webhook processing
- Test permissions by role
- Test invoice/payment flows
- Test admin-only routes

9. Post-deploy
- Verify the app responds over HTTPS only
- Confirm CSP and security headers are present in production
- Confirm rate-limit enforcement is active
- Confirm no secrets are visible in error output
