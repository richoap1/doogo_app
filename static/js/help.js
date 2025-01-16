$(document).ready(function() {
    $('#send-button').click(function() {
        var userInput = $('#user-input').val();
        if (userInput) {
            // Append user message to chat box
            $('#chat-box').append('<div class="user-message">' + userInput + '</div>');
            $('#user-input').val(''); // Clear input field

            // Send user message to the server
            $.post('/bantuan', { user_message: userInput }, function() {
                // Simulate admin response after a short delay
                setTimeout(function() {
                    $('#chat-box').append('<div class="admin-message">Admin: Thank you for your message!</div>');
                    // Scroll to the bottom of the chat box
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }, 1000);
            });
        }
    });

    // Optional: Allow pressing "Enter" to send the message
    $('#user-input').keypress(function(e) {
        if (e.which === 13) { // Enter key
            $('#send-button').click();
            e.preventDefault(); // Prevent form submission
        }
    });
});
