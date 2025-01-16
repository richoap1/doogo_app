// Select DOM elements
const openChatButton = document.getElementById('open-chat');
const chatPopup = document.getElementById('chat-popup');
const closeChatButton = document.getElementById('close-chat');
const sendButton = document.getElementById('send-button');
const userInput = document.getElementById('user-input');
const chatBody = document.getElementById('chat-body');

// Open chat popup
openChatButton.addEventListener('click', () => {
    chatPopup.style.display = 'block';
});

// Close chat popup
closeChatButton.addEventListener('click', () => {
    chatPopup.style.display = 'none';
});

// Send message
sendButton.addEventListener('click', () => {
    const message = userInput.value.trim();
    if (message) {
        addMessageToChat('You', message);
        userInput.value = '';
        getBotResponse(message);
    }
});

// Function to add messages to the chat
function addMessageToChat(sender, message) {
    const messageElement = document.createElement('div');
    messageElement.classList.add('chat-message');
    messageElement.innerHTML = `<strong>${sender}:</strong> ${message}`;
    chatBody.appendChild(messageElement);
    chatBody.scrollTop = chatBody.scrollHeight; // Scroll to the bottom
}

// Function to get bot response
function getBotResponse(userMessage) {
    let botMessage = "Saya tidak yakin bagaimana menjawab itu.";
    
    // Simple rule-based responses in Indonesian
    const lowerCaseMessage = userMessage.toLowerCase();
    if (lowerCaseMessage.includes("halo") || lowerCaseMessage.includes("hai")) {
        botMessage = "Halo! Bagaimana saya bisa membantu Anda hari ini?";
    } else if (lowerCaseMessage.includes("selamat tinggal") || lowerCaseMessage.includes("bye")) {
        botMessage = "Selamat tinggal! Semoga hari Anda menyenangkan!";
    } else if (lowerCaseMessage.includes("bantuan")) {
        botMessage = "Tentu! Apa yang bisa saya bantu?";
    } else if (lowerCaseMessage.includes("apa kabar")) {
        botMessage = "Saya baik-baik saja, terima kasih! Bagaimana dengan Anda?";
    } else if (lowerCaseMessage.includes("terima kasih")) {
        botMessage = "Sama-sama! Jika ada yang lain, silakan beri tahu saya.";
    } else if (lowerCaseMessage.includes("siapa kamu")) {
        botMessage = "Saya adalah chatbot yang siap membantu Anda!";
    } else if (lowerCaseMessage.includes("informasi")) {
        botMessage = "Tentu! Informasi apa yang Anda butuhkan?";
    } else if (lowerCaseMessage.includes("produk")) {
        botMessage = "Kami memiliki berbagai produk. Apa yang Anda cari?";
    } else if (lowerCaseMessage.includes("layanan")) {
        botMessage = "Kami menawarkan berbagai layanan. Apa yang Anda butuhkan?";
    } else if (lowerCaseMessage.includes("jadwal")) {
        botMessage = "Silakan beri tahu saya jadwal apa yang Anda maksud.";
    }

    // Add WhatsApp message
    botMessage += '<br><br>Untuk pertanyaan lebih lanjut, Anda bisa menuju ke <a href="https://wa.me/+6281234546467?text=Saya%20Customer%20Doogo%20Ingin%20Menanyakan%20Sesuat%20Tentang..." target="_blank">WhatsApp admin kami</a>.';

    // Add bot response to chat
    addMessageToChat('DG Assistant', botMessage);
}

// Function to handle predefined questions
function handlePredefinedQuestion(question) {
    // Simulate user input
    addMessageToChat('User', question);

    // Get bot response
    getBotResponse(question);
}
