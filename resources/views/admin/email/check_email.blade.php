<!DOCTYPE html>
<html>
    <head>
        <title>New Contact Message</title>
    </head>
    <body>
        <h2>Thông tin liên hệ </h2>
        <p><strong>Name:</strong> {{ $contact->username }}</p>
        <p><strong>Email:</strong> {{ $contact->email }}</p>
        <p><strong>Phone:</strong> {{ $contact->phone }}</p>
        <p><strong>Nội dung:</strong> {{ $contact->content }}</p>
    </body>
</html>
