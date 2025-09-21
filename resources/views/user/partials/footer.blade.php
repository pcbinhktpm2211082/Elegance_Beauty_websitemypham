<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h4>Liên Hệ</h4>
            <p>Email: support@elegancebeauty.com</p>
            <p>Hotline: 1900 6868</p>
        </div>
        <div class="footer-section">
            <h4>Chính Sách</h4>
            <ul>
                <li><a href="{{ url('/shipping') }}">Vận Chuyển</a></li>
                <li><a href="{{ url('/returns') }}">Đổi Trả</a></li>
                <li><a href="{{ url('/privacy') }}">Bảo Mật</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Kết Nối</h4>
            <div class="social-icons">
                <a href="https://www.facebook.com/elegancebeauty" target="_blank" rel="noopener">FB</a>
                <a href="https://www.instagram.com/elegancebeauty" target="_blank" rel="noopener">IG</a>
                <a href="https://www.tiktok.com/@elegancebeauty" target="_blank" rel="noopener">TikTok</a>
            </div>
        </div>
    </div>
    <div class="copyright">
        © {{ date('Y') }} Elegance Beauty. Bản Quyền Thuộc Về Elegance Beauty.
    </div>
</footer>
