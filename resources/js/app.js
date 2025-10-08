import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/app.css'; // or your main CSS file
// Import QRCode generator
import qrcode from 'qrcode-generator';

// Make it available globally
window.qrcode = qrcode;