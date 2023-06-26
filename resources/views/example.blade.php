<!DOCTYPE html>
<html>
<head>
    <title>Localization Example</title>
</head>
<body>
<h1>{{ __('messages.welcome') }}</h1>
<p>{{ __('This is an example of localization in Laravel') }}</p>

<form action="/change-locale" method="get">
    @csrf
    <label for="locale-select">Select Language:</label>
    <select id="locale-select" name="locale">
        <option value="en">English</option>
        <option value="bn">Bengali</option>
    </select>
    <button type="submit">Change Language</button>
</form>



</body>
</html>
