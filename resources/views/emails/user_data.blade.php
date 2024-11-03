<!DOCTYPE html>
<html>
<head>
    <title>{{ $user->nis }}</title>
</head>
<body>
    <h1>User Data</h1>
    <p>NIS: {{ $user->nis }}</p>
    <p>Name: {{ $user->name }}</p>
    <p>Email: {{ $user->email }}</p>
    <p>Phone Number: {{ $user->phone_number }}</p>
    <p>School: {{ $user->school }}</p>
    <p>Temporary Password: {{ $user->password }}</p>
</body>
</html>
