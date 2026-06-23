<!-- Chatbot Widget -->
<div id="chatbot-widget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end" style="font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    
    <!-- Khung Chat (Ẩn mặc định) -->
    <div id="chatbot-window" class="hidden w-[360px] sm:w-[420px] bg-gradient-to-b from-[#FFF5F0] to-white rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(255,107,53,0.2)] overflow-hidden transition-all duration-500 transform origin-bottom-right scale-95 opacity-0 mb-4 flex-col h-[600px] border border-orange-100">
        
        <!-- Header -->
        <div class="bg-[#FF6B35] text-white p-5 flex items-center justify-between z-10 relative overflow-hidden rounded-t-[2rem]">
            <!-- Decorative circles -->
            <div class="absolute top-[-20px] right-[20px] w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            <div class="absolute bottom-[-10px] right-[60px] w-16 h-16 bg-white opacity-10 rounded-full blur-lg"></div>
            
            <div class="flex items-center space-x-3 relative z-10">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-inner overflow-hidden border border-white/50 p-0.5">
                    <img src="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780496206/5drg92D3VeOdSV5C41Lipg_2k_q40cvj.webp" alt="SunFlower Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h3 class="font-bold text-[17px] tracking-wide flex items-center gap-1 leading-tight">
                        SunFlower
                    </h3>
                    <p class="text-[11px] text-white/90 font-medium flex items-center gap-1.5 mt-1">
                        <span class="w-2 h-2 rounded-full bg-[#4ADE80] shadow-[0_0_8px_rgba(74,222,128,0.8)] relative">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-[#4ADE80] opacity-75 animate-ping"></span>
                        </span> 
                        Đang trực tuyến
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2 relative z-10">
                <button class="w-8 h-8 flex items-center justify-center rounded-full text-white/80 hover:text-white hover:bg-white/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>
                <button id="chatbot-close-btn" class="w-8 h-8 flex items-center justify-center rounded-full text-white/80 hover:text-white hover:bg-white/20 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        <!-- Chat History -->
        <div id="chatbot-messages" class="flex-1 p-5 overflow-y-auto bg-transparent flex flex-col space-y-4 custom-scrollbar">
            <!-- Lời chào mặc định -->
            <div class="flex items-start max-w-[90%]">
                <div class="w-8 h-8 rounded-full bg-white flex-shrink-0 flex items-center justify-center mr-3 shadow-md mt-1 overflow-hidden p-0.5 border border-[#FF6B35]/20">
                    <img src="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780496206/5drg92D3VeOdSV5C41Lipg_2k_q40cvj.webp" alt="SunFlower" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col space-y-3 w-full">
                    <div class="bg-white p-4 rounded-2xl rounded-tl-sm shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] text-[14px] text-gray-800 leading-relaxed border border-orange-50/50">
                        Xin chào bạn! 👋<br><br>
                        Chào mừng đến với tiệm hoa <b>SunFlower</b>. Tôi sẽ giúp bạn chọn được bó hoa ưng ý nhất!<br><br>
                        Bạn cần tư vấn gì hôm nay?
                    </div>
                    
                    <!-- Vertical Quick Replies -->
                    <div class="flex flex-col space-y-2 w-full" id="chatbot-quick-replies">
                        <button type="button" class="chatbot-quick-reply text-left px-4 py-2.5 rounded-xl border border-[#FFB89E] bg-[#FFF5F0] text-[#D9531E] text-[13px] font-medium hover:bg-[#FFE1D6] transition-colors focus:outline-none">💐 Chọn hoa theo dịp</button>
                        <button type="button" class="chatbot-quick-reply text-left px-4 py-2.5 rounded-xl border border-[#FFB89E] bg-[#FFF5F0] text-[#D9531E] text-[13px] font-medium hover:bg-[#FFE1D6] transition-colors focus:outline-none">🌷 Tìm theo loại hoa</button>
                        <button type="button" class="chatbot-quick-reply text-left px-4 py-2.5 rounded-xl border border-[#FFB89E] bg-[#FFF5F0] text-[#D9531E] text-[13px] font-medium hover:bg-[#FFE1D6] transition-colors focus:outline-none">💝 Hoa tặng người thân</button>
                    </div>
                    
                    <div class="text-[11px] text-gray-400 mt-1 pl-1" id="greeting-time"></div>
                </div>
            </div>
        </div>

        <!-- Suggested Chips -->
        <div class="px-5 pb-3 bg-transparent flex space-x-2 overflow-x-auto custom-scrollbar whitespace-nowrap">
            <button type="button" class="chatbot-chip bg-white border border-[#FFB89E] text-[#D9531E] text-[13px] px-4 py-1.5 rounded-full hover:bg-[#FFF5F0] transition-colors shadow-sm focus:outline-none shrink-0">Hoa sinh nhật</button>
            <button type="button" class="chatbot-chip bg-white border border-[#FFB89E] text-[#D9531E] text-[13px] px-4 py-1.5 rounded-full hover:bg-[#FFF5F0] transition-colors shadow-sm focus:outline-none shrink-0">Hoa tặng bạn gái</button>
            <button type="button" class="chatbot-chip bg-white border border-[#FFB89E] text-[#D9531E] text-[13px] px-4 py-1.5 rounded-full hover:bg-[#FFF5F0] transition-colors shadow-sm focus:outline-none shrink-0">Bảng giá</button>
            <button type="button" class="chatbot-chip bg-white border border-[#FFB89E] text-[#D9531E] text-[13px] px-4 py-1.5 rounded-full hover:bg-[#FFF5F0] transition-colors shadow-sm focus:outline-none shrink-0">Giao hỏa tốc</button>
        </div>

        <!-- Input Area -->
        <div class="px-5 pb-2 bg-transparent flex flex-col">
            <form id="chatbot-form" class="flex items-center space-x-2 relative">
                <div class="relative flex-1">
                    <textarea 
                        id="chatbot-input" 
                        rows="1" 
                        placeholder="Nhắn gì đó với SunFlower..." 
                        class="w-full bg-[#FFF5F0] text-[14px] pl-4 pr-10 py-3 rounded-3xl focus:outline-none focus:ring-1 focus:ring-[#FFB89E] border border-[#FFE1D6] resize-none max-h-24 overflow-y-auto text-gray-700 placeholder-[#FFB89E] custom-scrollbar shadow-inner"
                        style="min-height: 44px; line-height: 22px;"
                    ></textarea>
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#FFB89E] hover:text-[#FF6B35] focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                </div>
                <button type="submit" id="chatbot-submit-btn" class="w-11 h-11 rounded-full text-[#FF6B35] hover:bg-[#FFF5F0] hover:shadow-sm transition-all focus:outline-none flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 transform translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
            
            <div class="text-center mt-3 pb-2 text-[10px] text-gray-400 flex items-center justify-center gap-1.5 font-medium uppercase tracking-wider">
                <span class="text-[#FF6B35] text-[12px]">🎁</span> SunFlower <span class="text-gray-300">•</span> Mang yêu thương qua từng cánh hoa
            </div>
        </div>
    </div>

    <!-- Floating Button -->
    <button id="chatbot-toggle-btn" class="bg-[#FF6B35] text-white w-16 h-16 rounded-full flex items-center justify-center shadow-[0_8px_25px_-5px_rgba(255,107,53,0.5)] hover:shadow-[0_12px_30px_-5px_rgba(255,107,53,0.6)] hover:-translate-y-1 transition-all duration-500 focus:outline-none border-[3px] border-white group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white group-hover:scale-110 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </button>
</div>

<style>
    /* Tùy chỉnh scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #FFE1D6; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #FFB89E; }
    
    /* Animation cho Typing Indicator */
    .typing-dot { animation: typing 1.5s infinite ease-in-out both; }
    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }
    @keyframes typing {
        0%, 80%, 100% { transform: scale(0); opacity: 0.4; background-color: #FFE1D6; }
        40% { transform: scale(1); opacity: 1; background-color: #FF6B35; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotWindow = document.getElementById('chatbot-window');
    const toggleBtn = document.getElementById('chatbot-toggle-btn');
    const closeBtn = document.getElementById('chatbot-close-btn');
    const chatForm = document.getElementById('chatbot-form');
    const chatInput = document.getElementById('chatbot-input');
    const chatMessages = document.getElementById('chatbot-messages');
    const submitBtn = document.getElementById('chatbot-submit-btn');

    let isWaiting = false;

    // Set time for greeting
    const now = new Date();
    const timeString = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
    const timeEl = document.getElementById('greeting-time');
    if (timeEl) timeEl.innerText = timeString;

    // Các icon SVG để thay đổi trạng thái
    const iconChat = `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white group-hover:scale-110 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>`;
    const iconClose = `<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>`;

    // Toggle khung chat với hiệu ứng spring
    function toggleChat() {
        if (chatbotWindow.classList.contains('hidden')) {
            chatbotWindow.classList.remove('hidden');
            chatbotWindow.classList.add('flex');
            
            setTimeout(() => {
                chatbotWindow.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
                chatbotWindow.classList.add('scale-100', 'opacity-100', 'translate-y-0');
                chatInput.focus();
            }, 10);
            toggleBtn.innerHTML = iconClose;
        } else {
            chatbotWindow.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            chatbotWindow.classList.add('scale-95', 'opacity-0', 'translate-y-4');
            setTimeout(() => {
                chatbotWindow.classList.remove('flex');
                chatbotWindow.classList.add('hidden');
            }, 400);
            toggleBtn.innerHTML = iconChat;
        }
    }

    toggleBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);

    // Tự động kéo cuộn xuống cuối mượt mà
    function scrollToBottom() {
        chatMessages.scrollTo({
            top: chatMessages.scrollHeight,
            behavior: 'smooth'
        });
    }

    // Template tin nhắn User
    function getUserMessageTemplate(content) {
        return `
            <div class="bg-white border border-[#FFB89E]/40 text-[#D9531E] py-3 px-4 rounded-[20px] rounded-tr-[4px] shadow-[0_2px_10px_-3px_rgba(255,107,53,0.1)] text-[14px] break-words">
                ${content}
            </div>
        `;
    }

    // Template tin nhắn AI
    function getAiMessageTemplate(content) {
        let formattedContent = content.replace(/\n/g, '<br>');
        formattedContent = formattedContent.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" class="text-[#D9531E] font-bold underline hover:text-[#E85D22] transition-colors">$1</a>');
        
        return `
            <div class="w-8 h-8 rounded-full bg-white flex-shrink-0 flex items-center justify-center mr-3 shadow-md mt-1 overflow-hidden p-0.5 border border-[#FF6B35]/20">
                <img src="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780496206/5drg92D3VeOdSV5C41Lipg_2k_q40cvj.webp" alt="SunFlower" class="w-full h-full object-contain">
            </div>
            <div class="bg-white p-4 rounded-2xl rounded-tl-sm shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] text-[14px] text-gray-800 leading-relaxed border border-orange-50/50 break-words w-full">
                <div class="prose prose-sm prose-p:my-1 prose-a:text-[#D9531E] max-w-none">
                    ${formattedContent}
                </div>
            </div>
        `;
    }

    // Thêm tin nhắn vào khung chat với hiệu ứng trượt
    function appendMessage(content, isUser = false) {
        const div = document.createElement('div');
        div.className = isUser 
            ? 'flex items-end justify-end max-w-[85%] self-end translate-y-4 opacity-0 transition-all duration-500 ease-out font-sans' 
            : 'flex items-start max-w-[85%] self-start translate-y-4 opacity-0 transition-all duration-500 ease-out w-full font-sans';

        div.innerHTML = isUser ? getUserMessageTemplate(content) : getAiMessageTemplate(content);
        
        chatMessages.appendChild(div);
        
        // Trigger animation
        setTimeout(() => {
            div.classList.remove('translate-y-4', 'opacity-0');
            div.classList.add('translate-y-0', 'opacity-100');
            scrollToBottom();
        }, 10);
    }

    // Hiện hiệu ứng Đang gõ
    function showTypingIndicator() {
        const id = 'typing-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'flex items-center max-w-[85%] self-start translate-y-2 opacity-0 transition-all duration-300 font-sans';
        div.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-white flex-shrink-0 flex items-center justify-center mr-3 shadow-md overflow-hidden p-0.5 border border-[#FF6B35]/20">
                <img src="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780496206/5drg92D3VeOdSV5C41Lipg_2k_q40cvj.webp" alt="SunFlower" class="w-full h-full object-contain">
            </div>
            <div class="bg-white py-4 px-5 rounded-2xl rounded-tl-sm shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] text-sm flex items-center space-x-1.5 border border-orange-50/50">
                <div class="w-1.5 h-1.5 bg-[#FFB89E] rounded-full typing-dot"></div>
                <div class="w-1.5 h-1.5 bg-[#FF8C61] rounded-full typing-dot"></div>
                <div class="w-1.5 h-1.5 bg-[#FF6B35] rounded-full typing-dot"></div>
            </div>
        `;
        chatMessages.appendChild(div);
        
        setTimeout(() => {
            div.classList.remove('translate-y-2', 'opacity-0');
            div.classList.add('translate-y-0', 'opacity-100');
            scrollToBottom();
        }, 10);
        
        return id;
    }

    // Xóa hiệu ứng Đang gõ
    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('opacity-0', 'scale-95');
            setTimeout(() => el.remove(), 300);
        }
    }

    // Xử lý sự kiện click vào Chips ngang
    const chips = document.querySelectorAll('.chatbot-chip');
    chips.forEach(chip => {
        chip.addEventListener('click', function() {
            if (isWaiting) return;
            chatInput.value = this.innerText;
            chatForm.dispatchEvent(new Event('submit', { cancelable: true }));
        });
    });

    // Xử lý Quick Replies dọc
    const quickReplies = document.querySelectorAll('.chatbot-quick-reply');
    const quickRepliesContainer = document.getElementById('chatbot-quick-replies');
    quickReplies.forEach(btn => {
        btn.addEventListener('click', function() {
            if (isWaiting) return;
            // Bỏ emoji ở đầu nếu có (ví dụ "💐 Chọn hoa" -> "Chọn hoa")
            chatInput.value = this.innerText.replace(/^[^\s]+\s/, '');
            chatForm.dispatchEvent(new Event('submit', { cancelable: true }));
            
            // Ẩn bảng Quick Replies đi cho gọn
            if (quickRepliesContainer) {
                quickRepliesContainer.style.display = 'none';
            }
        });
    });

    // Auto-resize textarea
    chatInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight < 96 ? this.scrollHeight : 96) + 'px';
    });

    // Bắt sự kiện Enter (nếu không giữ Shift)
    chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // Xử lý gửi Form
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = chatInput.value.trim();
        if (!message || isWaiting) return;

        chatInput.value = '';
        chatInput.style.height = 'auto';
        
        appendMessage(message, true);
        
        isWaiting = true;
        chatInput.disabled = true;
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'scale-95');
        
        // Cũng ẩn Quick Replies nếu người dùng tự gõ
        if (quickRepliesContainer && quickRepliesContainer.style.display !== 'none') {
            quickRepliesContainer.style.display = 'none';
        }
        
        const typingId = showTypingIndicator();

        fetch('{{ route("chatbot.ask") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            removeTypingIndicator(typingId);
            setTimeout(() => {
                if (data.reply) {
                    appendMessage(data.reply, false);
                } else {
                    appendMessage("Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.", false);
                }
            }, 300);
        })
        .catch(error => {
            removeTypingIndicator(typingId);
            setTimeout(() => {
                appendMessage("Lỗi kết nối mạng, vui lòng kiểm tra lại mạng hoặc thử lại sau.", false);
            }, 300);
        })
        .finally(() => {
            isWaiting = false;
            chatInput.disabled = false;
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'scale-95');
            chatInput.focus();
        });
    });
});
</script>
