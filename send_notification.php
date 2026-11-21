<?php
// send_notification.php - Send push notifications via Firebase Cloud Messaging

$serviceAccountPath = __DIR__ . '/firebase-service-account.json';
$uploadDir = __DIR__ . '/uploads/notifications/';
$message = '';
$messageType = '';

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $topic = trim($_POST['topic'] ?? 'all');
    
    if (empty($title) || empty($body)) {
        $message = 'Title and Description are required.';
        $messageType = 'error';
    } else {
        // Handle image upload
        $imageUrl = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);
            
            if (in_array($fileType, $allowedTypes)) {
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                    $imageUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/uploads/notifications/' . $fileName;
                }
            }
        }
        
        // Load service account
        if (!file_exists($serviceAccountPath)) {
            $message = 'Firebase service account file not found.';
            $messageType = 'error';
        } else {
            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
            
            // Get access token
            $accessToken = getAccessToken($serviceAccount);
            
            if (!$accessToken) {
                $message = 'Failed to get access token from Firebase.';
                $messageType = 'error';
            } else {
                // Send notification
                $result = sendFCMNotification($accessToken, $serviceAccount['project_id'], $title, $body, $imageUrl, $topic);
                
                if ($result['success']) {
                    $message = 'Notification sent successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to send notification: ' . ($result['error'] ?? 'Unknown error');
                    $messageType = 'error';
                }
            }
        }
    }
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function getAccessToken($serviceAccount) {
    $header = ["alg" => "RS256", "typ" => "JWT"];
    $now = time();
    $claims = [
        "iss" => $serviceAccount['client_email'],
        "scope" => "https://www.googleapis.com/auth/firebase.messaging",
        "aud" => $serviceAccount['token_uri'],
        "iat" => $now,
        "exp" => $now + 3600
    ];
    
    $headerEncoded = base64url_encode(json_encode($header));
    $claimsEncoded = base64url_encode(json_encode($claims));
    $signatureInput = $headerEncoded . "." . $claimsEncoded;
    
    $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
    if (!$privateKey) {
        return null;
    }
    
    openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $signatureEncoded = base64url_encode($signature);
    $jwt = $signatureInput . "." . $signatureEncoded;
    
    $ch = curl_init($serviceAccount['token_uri']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

function sendFCMNotification($accessToken, $projectId, $title, $body, $imageUrl) {
    $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
    
    $message = [
        'topic' => 'all',
        'notification' => [
            'title' => $title,
            'body' => $body
        ],
        'data' => [
            'image' => $imageUrl,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
        ]
    ];
    
    if (!empty($imageUrl)) {
        $message['android'] = [
            'notification' => [
                'image' => $imageUrl,
                'sound' => 'default'
            ]
        ];
        $message['apns'] = [
            'payload' => [
                'aps' => [
                    'mutable-content' => 1,
                    'sound' => 'default'
                ]
            ],
            'fcm_options' => [
                'image' => $imageUrl
            ]
        ];
    }
    
    $payload = ['message' => $message];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'error' => $data['error']['message'] ?? null,
        'response' => $data
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #FAFAFA;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
        .container {
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        padding: 40px;
        width: 100%;
        max-width: 500px;
    }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #EF6C00;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        .file-upload {
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .file-upload:hover {
            border-color: #EF6C00;
            background: #FFF8F1;
        }
        .file-upload input {
            display: none;
        }
        .file-upload-text {
            color: #666;
            font-size: 14px;
        }
        .file-name {
            color: #EF6C00;
        }
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-primary {
            background: #EF6C00;
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .message {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }
        .preview {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .preview img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }
        .preview-title {
            font-weight: 600;
            color: #333;
        }
        .preview-body {
            color: #666;
            margin-top: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nex-Drop</h1>
        <p class="subtitle">Send Notifications to all app users</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>" id="messageBox">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" id="notificationForm">
            <div class="form-group">
                <label for="title">Heading / Title *</label>
                <input type="text" id="title" name="title" placeholder="Enter notification title" required>
            </div>
            
            <div class="form-group">
                <label for="body">Description / Body *</label>
                <textarea id="body" name="body" placeholder="Enter notification description" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Image (Optional)</label>
                <div class="file-upload" onclick="document.getElementById('image').click()">
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                    <div class="file-upload-text">Click to upload image</div>
                    <div class="file-name" id="fileName"></div>
                </div>
                <div class="preview" id="imagePreview" style="display:none;">
                    <div class="preview-title">Preview:</div>
                    <img id="previewImg" src="" alt="Preview">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" id="sendBtn">Send Notification</button>
        </form>
    </div>
    
    <script>
        function previewImage(input) {
            const fileName = document.getElementById('fileName');
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                fileName.textContent = input.files[0].name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        document.getElementById('notificationForm').addEventListener('submit', function() {
            document.getElementById('sendBtn').disabled = true;
            document.getElementById('sendBtn').textContent = 'Sending...';
        });
        
        // Auto-hide message after 5 seconds
        const messageBox = document.getElementById('messageBox');
        if (messageBox) {
            setTimeout(function() {
                messageBox.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
