@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <ul class="list-group">
                <li class="list-group-item disabled">Users</li>
                @foreach($users as $aUser)
                <li class="list-group-item"><a href="<?php
                    $userId = Auth::user()->id;
                    $posterId = $aUser->id;

                    $party1 = min($userId, $posterId);
                    $party2 = max($userId, $posterId);

                    $secret = md5("$party1###$party2");
                    //$code = base64_url_encode("$userId#$posterId#$secret");

                    echo url("message/$secret/$userId/$posterId")
                    ?>">{{$aUser->name}}</a></li>
                @endforeach
            </ul>
            
            
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Active Chats</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif


                    <div id="vue-app">                       
                        <ul class="list-group">
                            <li class="list-group-item" v-for='(thread, index) in threads'>
                                <a v-bind:href="messageUrl(thread.thread,thread.sender_id,thread.receiver_id)"><strong>@{{thread.sender}} , @{{thread.receiver}} <span class="badge" v-if="thread.unread > 0">@{{thread.unread}}</span></strong></a>
                                <p>@{{thread.message}}</p>
                            </li>
                        </ul>
                        <!--<button v-on:click="getThreads">refresh</button>-->
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
    new Vue({
        el: '#vue-app',
        data: {
            name: 'Shabab',
            threads: [

            ]
        },
        created: function () {
            var self = this;
            setInterval(self.getThreads, 5000);
            self.getThreads();
        },
        methods: {
            messageUrl: function (code, sid, rid) {
                return "<?php echo url('message') ?>/" + code + "/" + sid + "/" + rid;
            },
            getThreads: function () {
                var self = this;
                $.ajax({
                    type: "GET",
                    url: "<?php echo url('api/threads/' . base64_url_encode($userId)) ?>",
                    dataType: "json",
                    success: function (result) {
                        self.threads = result;
                    }
                });
            }
        }
    });
</script>
@endpush