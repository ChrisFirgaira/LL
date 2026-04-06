<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>New contact enquiry</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #0f172a;">
        <h1 style="margin-bottom: 16px;">New storefront contact enquiry</h1>

        <p><strong>Name:</strong> {{ $contact['name'] }}</p>
        <p><strong>Email:</strong> {{ $contact['email'] }}</p>
        <p><strong>Subject:</strong> {{ $contact['subject'] }}</p>

        <h2 style="margin-top: 24px;">Message</h2>
        <p>{!! nl2br(e($contact['message'])) !!}</p>
    </body>
</html>
