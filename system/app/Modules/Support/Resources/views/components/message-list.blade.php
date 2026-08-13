@props(['messages' => []])

{{--
    Renders a batch of conversation messages. Used both for the initial page
    render and for each AJAX "load older" response, so the markup stays identical.
--}}
@foreach ($messages as $message)
    <x-support::message
        :name="$message['name']"
        :body="$message['body']"
        :at="$message['at']"
        :staff="$message['staff']"
    />
@endforeach
