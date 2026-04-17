import axios from 'axios';

// Gán axios vào cửa sổ trình duyệt để dùng ở mọi nơi
window.axios = axios;

// Cấu hình Header mặc định
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Cấu hình đường dẫn gốc
window.axios.defaults.baseURL = '/api';

// Gắn Token nếu có (để gọi các hàm cần đăng nhập)
const token = localStorage.getItem('access_token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

console.log("Axios đã sẵn sàng!"); // Dòng này để kiểm tra trong Console