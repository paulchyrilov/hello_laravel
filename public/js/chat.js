$(document).ready(function(){

    var chat = {
        user : user,
        userlist : users,
        currentRecipient : null,
        dialogs : [],
        newMessages : [],
        getUser : function(id) {
            for(k in this.userlist) {
                if(this.userlist.hasOwnProperty(k) && this.userlist[k].id == id) {
                    return this.userlist[k];
                }
            }
            return {id : id, name : 'Unknown'}
        },
        addIncomingMessage : function(message) {
            var userId;
            //Not realy incoming. Sercer returned our message back.
            if(message.sender_id == this.user) {
                userId = message.recipient_id;

            //Real incoming. We are recipient
            } else if (message.recipient_id == this.user) {
                userId = message.sender_id;
                if(userId != this.currentRecipient) {
                    this.newMessages[userId] = this.newMessages[userId] ? this.newMessages[userId] + 1 : 1;
                    $('#user_' + message.sender_id).addClass('list-group-item-warning').find('.badge').removeClass('hide').html(this.newMessages[userId]);

                }
            }
            if(this.dialogStarted(userId)) {
                this.addMessage(message);
                var $container = $("#chatMessages");
                $container.scrollTop($container[0].scrollHeight);
            }
        },
        addMessage : function(message) {
            if(message.sender_id == this.user) {
                this.addToDialog(message.recipient_id, 'You', message.created_at, message.text);
            } else if (message.recipient_id == this.user) {
                var user = this.getUser(message.sender_id);
                this.addToDialog(message.sender_id, user.username, message.created_at, message.text);
            }
        },
        loadHistory : function(id, messages) {
            this.startDialog(id);
            for(k in messages) {
                if (messages.hasOwnProperty(k)) {
                    this.addMessage(messages[k]);
                }
            }
        },
        addToDialog : function(id, user, time, text) {
            if(!this.dialogStarted(id)) {
                return;
            }
            this.dialogs[id].append($('<li>', {text: time + ' ' + user + ': ' + text}));
        },
        dialogStarted : function(id){
            return typeof this.dialogs[id] == 'object';
        },
        startDialog : function(id) {
            this.dialogs[id] = $('<ul>');
        },
        getDialog : function(id)
        {
            if(this.dialogStarted(id)) {
                return this.dialogs[id];
            }
            return '';
        }

    };

    $('#users').on('click', 'li', function(e){
        var $element = $(e.currentTarget),
            userId = $element.data('id');

        $('#users li').removeClass('list-group-item-success');
        $element.removeClass('list-group-item-warning').find('.badge').addClass('hide').html('');
        chat.newMessages[userId] = 0;
        chat.currentRecipient = userId;

        var $container = $("#chatMessages");

        if(!chat.dialogStarted(userId)) {
            $.get($element.data('history'), function(data){
                $element.addClass('list-group-item-success');
                chat.loadHistory(userId, data);
                $container.html(chat.getDialog(userId));
                $container.scrollTop($container[0].scrollHeight);
            })
        } else {
            $element.addClass('list-group-item-success');
            $container.html(chat.getDialog(userId));
            $container.scrollTop($container[0].scrollHeight);
        }
    });

    socket = new WebSocket('ws://'+uri+':'+port);

    socket.sendWhenReady = function(message, interval, failCallback){
        if (this.readyState === 1) {
            this.send(message);
        } else if(this.readyState == 0) {
            var that = this;
            // optional: implement backoff for interval here
            setTimeout(function () {
                that.sendWhenReady(message, interval);
            }, 1000);
        } else {
            console.log('The connection is in closing/closed state. Message can not be sent.');
            if(typeof failCallback == 'funtion') {
                failCallback();
            }
        }
    };

    socket.onopen = function(e)
    {
        socket.sendWhenReady(JSON.stringify({command: 'setUser', token : token}), 1000);

        $.notify("Connection established!", 'info');
    };

    socket.onmessage = function(e)
    {
        var message = JSON.parse(e.data);

        if(typeof message == 'object') {
            if(message.sender_id == chat.user && message.recipient_id == chat.currentRecipient) {
                $('#chatText').val('').removeAttr('disabled');
            }
            chat.addIncomingMessage(message);
        }
    };

    $('#chatText').keyup(function(e){
        if (e.keyCode == 13) // enter was pressed
        {

            if(socket.readyState != 1) {
                $.notify('Connection is not established.');
                return false;
            }

            if(!chat.currentRecipient) {
                $.notify('Select user at first', 'warn');
                return false;
            }

            var $input = $(this),
                messageText = $input.val(),
                message = {from:user, to: $('#users .list-group-item-success').data('id'), text: messageText};

            $input.attr('disabled', 'disabled');
            socket.sendWhenReady(JSON.stringify(message));


        }
    });

    socket.onclose = function (event) {
        var reason;

        if (event.code == 1000)
            reason = "Normal closure, meaning that the purpose for which the connection was established has been fulfilled.";
        else if(event.code == 1001)
            reason = "An endpoint is \"going away\", such as a server going down or a browser having navigated away from a page.";
        else if(event.code == 1002)
            reason = "An endpoint is terminating the connection due to a protocol error";
        else if(event.code == 1003)
            reason = "An endpoint is terminating the connection because it has received a type of data it cannot accept (e.g., an endpoint that understands only text data MAY send this if it receives a binary message).";
        else if(event.code == 1004)
            reason = "Reserved. The specific meaning might be defined in the future.";
        else if(event.code == 1005)
            reason = "No status code was actually present.";
        else if(event.code == 1006)
            reason = "Abnormal error, e.g., without sending or receiving a Close control frame";
        else if(event.code == 1007)
            reason = "An endpoint is terminating the connection because it has received data within a message that was not consistent with the type of the message (e.g., non-UTF-8 [http://tools.ietf.org/html/rfc3629] data within a text message).";
        else if(event.code == 1008)
            reason = "An endpoint is terminating the connection because it has received a message that \"violates its policy\". This reason is given either if there is no other sutible reason, or if there is a need to hide specific details about the policy.";
        else if(event.code == 1009)
            reason = "An endpoint is terminating the connection because it has received a message that is too big for it to process.";
        else if(event.code == 1010) // Note that this status code is not used by the server, because it can fail the WebSocket handshake instead.
            reason = "An endpoint (client) is terminating the connection because it has expected the server to negotiate one or more extension, but the server didn't return them in the response message of the WebSocket handshake. <br /> Specifically, the extensions that are needed are: " + event.reason;
        else if(event.code == 1011)
            reason = "A server is terminating the connection because it encountered an unexpected condition that prevented it from fulfilling the request.";
        else if(event.code == 1015)
            reason = "The connection was closed due to a failure to perform a TLS handshake (e.g., the server certificate can't be verified).";
        else
            reason = "Unknown reason";

        $.notify("Connection closed: " + reason,
            {
                clickToHide: true,
                autoHide: false,
                className: 'error'
            }
        );
    };
});