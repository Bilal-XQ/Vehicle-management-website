// Simple Node.js script to test CORS headers
const https = require('http');

const options = {
  hostname: 'localhost',
  port: 8000,
  path: '/simple-api.php/api/auth/login',
  method: 'OPTIONS',
  headers: {
    'Origin': 'http://localhost:5173',
    'Access-Control-Request-Method': 'POST',
    'Access-Control-Request-Headers': 'Content-Type, Accept'
  }
};

const req = https.request(options, (res) => {
  console.log(`Status: ${res.statusCode}`);
  console.log('Headers:', res.headers);
  
  res.on('data', (chunk) => {
    console.log('Body:', chunk.toString());
  });
});

req.on('error', (error) => {
  console.error('Error:', error);
});

req.end();
