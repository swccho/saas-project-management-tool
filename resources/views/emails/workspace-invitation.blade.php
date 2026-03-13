<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace Invitation</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #4f46e5;">You're invited to join {{ $workspace->name }}</h2>
    <p>{{ $inviter?->name ?? 'Someone' }} has invited you to join their workspace on {{ config('app.name') }}.</p>
    <p style="margin: 24px 0;">
        <a href="{{ $acceptUrl }}" style="display: inline-block; padding: 12px 24px; background-color: #4f46e5; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">Accept invitation</a>
    </p>
    <p style="margin-top: 24px; font-size: 14px; color: #6b7280;">This invitation expires in 7 days. If you don't have an account, you'll be able to create one when you accept.</p>
    <p style="margin-top: 24px; font-size: 12px; color: #9ca3af;">If you didn't expect this invitation, you can safely ignore this email.</p>
</body>
</html>
