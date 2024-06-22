@extends('layouts.app')

@section('content')
    <div class="container">
        <div style="display: flex; justify-content: center; align-items: center;">

            <div id="talkjs-container" style="width: 40%; margin: 30px; height: 500px;">
                <i>Loading chat...</i>
            </div>
            <div id="contacts-list">
                <h2>Contacts</h2>
            </div>

        </div>
    </div>
@endsection

@push('css')
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <style>
        #contacts-list {
            overflow-y: auto;
            width: 300px;
            height: 500px;
            background-color: #ECECEC;
            border-radius: 0.75rem;
            border: 1px solid #D4D4D4;
            font-family: 'Inter';

        }

        #contacts-list h2 {
            padding: 20px;
        }

        .contact-container {

            height: 50px;
            display: flex;
            padding: 5px 0;
            cursor: pointer;
            border-bottom: 1px solid #D4D4D4;
        }

        .contact-container:hover {
            background-color: #007DF9;
            color: #fff;
            font-weight: bold;
        }

        .contact-name {
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
        }
    </style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentUser = @json(auth()->user());
    const otherUsers = @json($users);

    // Populate the contacts list
    const contactsWrapper = document.getElementById('contacts-list');
    for (let user of otherUsers) {
        const username = user.name;

        const usernameDiv = document.createElement('div');
        usernameDiv.classList.add('contact-name');
        usernameDiv.innerText = username;

        const contactContainerDiv = document.createElement('div');
        contactContainerDiv.classList.add('contact-container');

        contactContainerDiv.appendChild(usernameDiv);
        contactsWrapper.appendChild(contactContainerDiv);
    }

    // Function to load TalkJS script
    function loadTalkJSScript(callback, errorCallback) {
        const script = document.createElement('script');
        script.src = 'https://cdn.talkjs.com/talk.js';
        script.onload = callback;
        script.onerror = errorCallback;
        document.body.appendChild(script);
    }

    // Function to initialize TalkJS
    function initializeTalkJS() {
        if (typeof Talk === 'undefined') {
            console.error('TalkJS is not defined');
            return;
        }

        console.log('TalkJS loaded, initializing...');

        let me = new Talk.User({
            id: currentUser.id,
            name: currentUser.name,
        });

        window.talkSession = new Talk.Session({
            appId: 'tqsx4DeW',
            me: me,
        });

        const chatbox = talkSession.createChatbox();
        chatbox.mount(document.getElementById('talkjs-container'));

        const contactsListDivs = document.getElementsByClassName('contact-container');

        Array.from(contactsListDivs).forEach(function(contactDiv, index) {
            contactDiv.addEventListener('click', function() {
                console.log('Contact clicked:', otherUsers[index].name); // Debug log
                const otherUser = new Talk.User(otherUsers[index]);
                const conversation = talkSession.getOrCreateConversation(Talk.oneOnOneId(me, otherUser));
                conversation.setParticipant(me);
                conversation.setParticipant(otherUser);
                chatbox.select(conversation);
            });
        });
    }

    loadTalkJSScript(
        function() {
            console.log('TalkJS script loaded successfully');
            setTimeout(initializeTalkJS, 100);
        },
        function() {
            console.error('Failed to load TalkJS script');
        }
    );
});
</script>
@endpush
