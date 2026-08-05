<!DOCTYPE html>
<html lang="en">
<head>
	@include('home.css')
    <style>
        .front-only {
            height: 100vh;
            overflow: hidden;
        }

        .content-page {
            background: #000;
            padding-top: 96px;
        }

        @media (max-width: 1199.98px) {
            .content-page {
                padding-top: 82px;
            }
        }
    </style>
</head>
@php
    $section = request('section');
@endphp
<body class="{{ !$section ? 'front-only' : 'content-page' }}" data-spy="scroll" data-target=".navbar" data-offset="40" id="home">
    @include('home.header')

    @if($section === 'about')
        @include('home.about')
    @elseif($section === 'food')
        @include('home.blog')
    @elseif($section === 'book')
        @include('home.book')
    @elseif($section === 'contact')
        @include('home.contact')
    @endif

    @auth
        @if(!$section)
            @include('home.chatbot')
        @endif
    @endauth
</body>
</html>
