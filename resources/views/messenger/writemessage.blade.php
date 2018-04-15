@extends('layouts.app')

@section('content')
<style>
    #vue-messages li.otherGuyDid{
        text-align: right;
        background-color: #f2ffff;
        border-radius: 20px;
        margin: 10px 0px;
        margin-left: auto;
        padding-right: 20px;
        border-bottom-right-radius: 0px;
        max-width: 90%;
    }
    #vue-messages li.iSentIt{
        text-align: left;
        border-radius: 20px;
        margin: 10px 0px;
        margin-right: auto;
        padding-left: 20px;
        border-bottom-left-radius: 0px;
        max-width: 90%;
    }

    #vue-messages .chat-composer{
        /*height: 100px;*/
    }
    #vue-messages li.msg-unread p{
        font-weight: bold;
    }

    #vue-messages li.msg-read p{
        font-weight: 300;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <a class="btn btn-primary" href="{{url('/home')}}">Back To inbox</a>
            <hr/>
            <ul class="list-group">
                <li class="list-group-item disabled">Users</li>
                @foreach($users as $aUser)
                <li class="list-group-item"><a href="<?php
                    $userId = Auth::user()->id;
                    $posterId = $aUser->id;

                    $party1 = min($userId, $posterId);
                    $party2 = max($userId, $posterId);

                    $secret = md5("$party1###$party2");

                    echo url("message/$secret/$userId/$posterId")
                    ?>">{{$aUser->name}}</a></li>
                @endforeach
            </ul>            
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{$user_other_name}}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div id="vue-messages">                         
                        <ul class="list-group">
                            <li v-bind:class="getMessageClass(thread.sender_id, thread.read_status)" v-for='thread in threads'>
                                <a><strong>@{{thread.sender}}</strong></a>
                                <p>@{{thread.message}}</p>
                            </li>
                        </ul>
                        <!--<button v-on:click="getMessagesInThread">refresh</button>-->
                        <form class="margin-bottom-60">
                            <div class="form-group">
                                <textarea id="messageComposerInput" class="form-control" ref="messageComposer" autofocus></textarea>
                            </div>                                
                            <div class="form-group">
                                <button type="button" class="btn btn-primary pull-right" @click.prevent="submitNewMessage()">@lang('Send')</button>
                                <!--<button class="btn btn-primary pull-right" @click.prevent="submitNewMessage()">@lang('Send')</button>-->
                            </div>
                            <!--<input class="form-control chat-composer" type="text" ref="messageComposer">-->                                
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("styles")
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
@endpush


@push("scripts")
<script type="text/javascript">

    function test() {
        console.log("t");
    }

    new Vue({
        el: '#vue-messages',
        data: {
            newmessage: 'none',
            threads: [

            ]
        },
        created: function () {
            var self = this;
            this.getMessagesInThread();
        },
        mounted: function () {
            console.log("ya");
            setInterval(function () {
                $('#messageComposerInput').focus();
            }, 5000);
        },
        methods: {
            getMessagesInThread: function () {
                var self = this;
                $.ajax({
                    type: "GET",
                    url: "<?php echo url('api/thread/' . $thread . '/' . $user_logged) ?>",
                    dataType: "json",
                    success: function (result) {
                        self.threads = result;
                        setTimeout(self.getMessagesInThread, 5000);
                    }
                });
            },
            getMessageClass(sender_id, read_status) {

                var css = 'list-group-item';
                if (sender_id === <?php echo $user_logged ?>) {
                    css = css + " iSentIt";
                } else {
                    css = css + " otherGuyDid";
                    if (read_status === 1) {
                        css = css + " msg-read";
                    } else {
                        css = css + " msg-unread";
                    }
                }
                return css;
            },
            submitNewMessage() {

                console.log("we are doing submit ");
                var self = this;
                var msgTyped = this.$refs.messageComposer.value;

                $.ajax({
                    type: "POST",
                    url: "<?php echo url('api/submitmessage') ?>",
                    data: {
                        _token: '{{csrf_token()}}',
                        sender_id: '<?php echo $user_logged ?>',
                        receiver_id: '<?php echo $user_other ?>',
                        thread: '{{$thread}}',
                        message: msgTyped
                    },
                    dataType: "json",
                    success: function (result) {
                        console.log("its gone");
                        self.threads.push({
                            sender_id: <?php echo $user_logged ?>,
                            sender: "<?php echo $user_logged_name ?>",
                            message: msgTyped
                        });
                    }
                });
                this.$refs.messageComposer.value = "";
            }
        }
    });

</script>
@endpush