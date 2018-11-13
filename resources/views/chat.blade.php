@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card chat-box">
                    <div class="card-header">Chat</div>
                    <div class="card-body" id="chat-list">
                        <div id="chat-message-list">
                            @if($count)
                                <p align="center">
                                    <button class="btn btn-sm" onclick="loadMore()" id="load-more">Load more..</button>
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr/>
                    <div class="send-message-form">
                        <form onsubmit="sendMessage(this,event)">
                            <div class="form-group row">
                                {!! csrf_field() !!}
                                <div class="col-md-10">
                                    <input name="message_content" id="message_content" class="form-control"
                                           placeholder="Type something ..." autocomplete="off">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-submit btn-custom">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script src="//{{ request()->getHost() }}:6001/socket.io/socket.io.js"></script>
    <script>
        $(document).ready(function () {
            window.Echo.channel('public').listen('.MessageSent', ({ message }) => {
                appendMessage(message);
            });
        });

        const chatItem = "<p class=\"${message_direction}\ message-block\">" +
            "<input type=\"hidden\" class=\"message-id\" value=\"${id}\">" +
            "<span class=\"user-name\"><b>${user_name}</b></span>" +
            "<span class=\"user-message-separator\">:</span>" +
            "<span class=\"message-content\">${message_content}</span>" +
            "<span class=\"message-time\"><sub>${created_at}</sub></span>" +
            "</p>";

        function appendMessage(messageObj) {
            messageObj.message_direction = messageObj.user_id === {{auth()->user()->id}} ? "message-self" : "message-others";
            messageObj.user_name = messageObj.user.name;
            $("#chat-message-list").append(templating(chatItem, messageObj));
            if (messageObj.id === {{$messages->first()->id}}) {
                $('#chat-message-list').animate({
                    scrollTop: $('#chat-message-list')[0].scrollHeight
                });
            }
        }

        @foreach($messages->sortBy('created_at') as $msg)
        appendMessage({!! $msg !!});

        @endforeach

        function sendMessage(form, event) {
            event.preventDefault();
            if ($('#message_content').val().trim()) {
                $.ajax({
                    url: "{{route('send-message')}}",
                    method: "POST",
                    data: $(form).serialize(),
                    success: function (response) {
                        appendMessage(response);
                    }
                })
                $(form)[0].reset();
                $('#chat-list').animate({
                    scrollTop: $('#chat-list')[0].scrollHeight
                });
            }
        }

        function prependMessage(messageObj) {
            messageObj.message_direction = messageObj.user_id === {{auth()->user()->id}} ? "message-self" : "message-others";
            messageObj.user_name = messageObj.user.name;
            $(".message-block:first").before(templating(chatItem, messageObj));
        }

        function loadMore() {
            $.ajax({
                url: "{{route('load-more')}}",
                method: "POST",
                data: { id: $('.message-id:first').val(), _token: "{{csrf_token()}}" },
                success: async function (parameters) {
                    for (i in parameters.messages) {
                        await prependMessage(parameters.messages[i]);
                    }
                    if (parameters.count === 0) {
                        $('#load-more').remove();
                    }
                }
            })
        }
    </script>
@endpush