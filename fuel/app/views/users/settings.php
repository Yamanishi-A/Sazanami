<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Sazanami</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#5ba3c5',
                        'primary-foreground': '#ffffff',
                        secondary: '#e0f2fe',
                        accent: '#bae6fd',
                        foreground: '#333333',
                        'muted-foreground': '#666666',
                        border: '#e5e7eb',
                        'input-background': '#ffffff'
                    }
                }
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.5.1/knockout-latest.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50">

<?php echo View::forge('shared/header', array('user' => isset($user) ? $user : null)); ?>

<div id="settings-page" class="min-h-screen relative overflow-hidden">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <svg class="absolute w-full h-full opacity-30" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="wave-pattern-settings" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                    <path d="M0 50 Q 25 35, 50 50 T 100 50" fill="none" stroke="#5ba3c5" strokeWidth="0.5" opacity="0.3" />
                    <path d="M0 60 Q 25 45, 50 60 T 100 60" fill="none" stroke="#9fd4e0" strokeWidth="0.5" opacity="0.4" />
                    <path d="M0 70 Q 25 55, 50 70 T 100 70" fill="none" stroke="#b8dfe8" strokeWidth="0.5" opacity="0.3" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#wave-pattern-settings)" />
        </svg>
    </div>

    <main class="max-w-4xl mx-auto px-6 py-12 relative z-10">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-foreground mb-2">Settings</h1>
            <p class="text-muted-foreground">Manage your profile and account security</p>
        </div>

        <div data-bind="visible: alertMessage, text: alertMessage, css: alertClass" style="display: none;" class="mb-6 p-4 rounded-xl font-bold transition-all"></div>

        <form data-bind="submit: handleSaveChanges" class="space-y-8">
            <div class="bg-white rounded-3xl p-8 shadow-lg border border-border">
                <div class="flex items-center gap-3 mb-6">
                    <i data-lucide="user" class="w-6 h-6 text-primary"></i>
                    <h2 class="text-2xl font-bold text-foreground">Public Profile</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold mb-3 text-foreground">Profile Picture</label>
                        <div class="flex items-center gap-6">
                            
                            <div class="relative group cursor-pointer" onclick="document.getElementById('icon-upload').click()">
                                <div class="relative w-32 h-32 rounded-full bg-gradient-to-br from-secondary to-accent flex items-center justify-center border-4 border-white shadow-md overflow-hidden">
                                    
                                    <img data-bind="visible: iconPreview, attr: { src: iconPreview }" class="absolute inset-0 w-full h-full object-cover" style="display: none;">
                                    
                                    <div data-bind="visible: !iconPreview()">
                                        <i data-lucide="user" class="w-16 h-16 text-primary"></i>
                                    </div>

                                </div>
                                <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-lucide="camera" class="w-8 h-8 text-white"></i>
                                </div>
                            </div>
                            
                            <div>
                                <button type="button" onclick="document.getElementById('icon-upload').click()" class="px-6 py-3 bg-secondary hover:bg-secondary/80 text-foreground font-bold rounded-xl transition-colors">
                                    Upload new photo
                                </button>
                                
                                <button type="button" data-bind="visible: iconPreview, click: removeIcon" style="display: none;" class="block mt-3 text-sm font-bold text-red-500 hover:text-red-600 transition-colors flex items-center gap-1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Remove photo
                                </button>
                                
                                <p class="text-sm text-muted-foreground mt-2">JPG, PNG or WEBP. Max size 5MB (Auto resized).</p>
                                
                                <input type="file" id="icon-upload" accept="image/*" class="hidden" data-bind="event: { change: handleImageUpload }">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-bold mb-2 text-foreground">Username</label>
                        <input id="username" type="text" data-bind="value: username" placeholder="Enter your username" required class="w-full px-4 py-3 bg-input-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all placeholder:text-muted-foreground">
                    </div>

                    <div>
                        <label for="profile-bio" class="block text-sm font-bold mb-2 text-foreground">Profile Bio / Description</label>
                        <textarea id="profile-bio" data-bind="value: profileBio" placeholder="Tell us about yourself and your music taste..." rows="5" class="w-full px-4 py-3 bg-input-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all placeholder:text-muted-foreground resize-none"></textarea>
                        <p class="text-xs text-muted-foreground mt-1">Share a little about yourself and your musical journey</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-lg border border-border">
                <div class="flex items-center gap-3 mb-6">
                    <i data-lucide="lock" class="w-6 h-6 text-primary"></i>
                    <h2 class="text-2xl font-bold text-foreground">Account Security</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-foreground">Email Address</label>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 px-4 py-3 bg-gray-50 border border-border rounded-xl text-muted-foreground">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="mail" class="w-4 h-4 text-primary"></i>
                                    <span data-bind="text: email"></span>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">This is your account email. Contact support to change it.</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <label for="new-password" class="block text-sm font-bold mb-2 text-foreground">Update Password</label>
                        <input id="new-password" type="password" data-bind="value: newPassword" placeholder="Enter new password" class="max-w-md w-full px-4 py-3 mb-3 bg-input-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all placeholder:text-muted-foreground">
                        <br>
                        <button type="button" data-bind="click: handlePasswordReset, disable: isSavingPassword" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-primary/90 hover:from-primary/90 hover:to-primary text-primary-foreground font-bold rounded-xl shadow-md hover:shadow-lg transition-all disabled:opacity-50">
                            <i data-lucide="key" class="w-4 h-4"></i>
                            <span data-bind="text: isSavingPassword() ? 'Updating...' : 'Save New Password'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-center">
                <button type="submit" data-bind="disable: isSavingProfile" class="inline-flex items-center gap-3 px-10 py-4 bg-accent hover:bg-accent/90 text-foreground font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    <span data-bind="text: isSavingProfile() ? 'Saving...' : 'Save Profile Changes'"></span>
                </button>
            </div>
        </form>

        <div class="mt-16 pt-8 border-t border-red-200">
            <h3 class="text-red-600 font-bold mb-2 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i> Danger Zone
            </h3>
            <p class="text-sm text-muted-foreground mb-4">Once you delete your account, there is no going back. Please be certain.</p>
            <button type="button" data-bind="click: handleDeleteAccount" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                <span>Delete Account</span>
            </button>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();

    function UserSettingsViewModel() {
        let self = this;

        // DB情報の読み込み
        self.username = ko.observable("<?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>");
        self.profileBio = ko.observable("<?php echo htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>");
        self.email = ko.observable("<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>");
        
        // アイコン関連
        self.iconPreview = ko.observable("<?php echo isset($user['icon']) ? $user['icon'] : ''; ?>");
        self.iconFile = ko.observable(null);
        self.removeIconFlag = ko.observable(false);

        // パスワード関連
        self.newPassword = ko.observable("");
        
        // UI状態管理
        self.isSavingProfile = ko.observable(false);
        self.isSavingPassword = ko.observable(false);
        self.alertMessage = ko.observable("");
        self.alertClass = ko.observable("");

        // アラート表示用ヘルパー
        self.showAlert = function(message, type) {
            self.alertMessage(message);
            if (type === 'success') {
                self.alertClass("bg-green-100 text-green-800 border border-green-300");
            } else {
                self.alertClass("bg-red-100 text-red-800 border border-red-300");
            }
            setTimeout(function() { self.alertMessage(""); }, 5000);
        };

        // Canvasによる画像圧縮アップロード処理
        self.handleImageUpload = function(data, event) {
            let file = event.target.files[0];
            if (!file) return;
            
            if (!file.type.match('image.*')) {
                self.showAlert("画像ファイルを選択してください。", "error");
                return;
            }

            let reader = new FileReader();
            reader.onload = function(e) {
                let img = new Image();
                img.onload = function() {
                    // プロフィールアイコン用に正方形（最大400x400）にリサイズ
                    let MAX_SIZE = 400;
                    let canvas = document.createElement('canvas');
                    canvas.width = MAX_SIZE;
                    canvas.height = MAX_SIZE;
                    let ctx = canvas.getContext('2d');
                    
                    // アスペクト比を保ちながら中央でクロップする計算
                    let scale = Math.max(MAX_SIZE / img.width, MAX_SIZE / img.height);
                    let x = (MAX_SIZE / scale - img.width) / 2;
                    let y = (MAX_SIZE / scale - img.height) / 2;
                    
                    ctx.drawImage(img, x, y, img.width, img.height, 0, 0, MAX_SIZE, MAX_SIZE);
                    
                    let dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                    self.iconPreview(dataUrl);
                    
                    // Blobへ変換
                    let bin = atob(dataUrl.split(',')[1]);
                    let buffer = new Uint8Array(bin.length);
                    for (let i = 0; i < bin.length; i++) buffer[i] = bin.charCodeAt(i);
                    self.iconFile(new Blob([buffer.buffer], {type: 'image/jpeg'}));
                    self.removeIconFlag(false);
                    
                    setTimeout(function() { lucide.createIcons(); }, 10);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        };

        self.removeIcon = function() {
            self.iconPreview(null);
            self.iconFile(null);
            self.removeIconFlag(true);
            let fileInput = document.getElementById('icon-upload');
            if (fileInput) fileInput.value = ""; 
        };

        // プロフィール保存処理
        self.handleSaveChanges = function() {
            if (!self.username().trim()) {
                self.showAlert("Username is required.", "error");
                return;
            }

            self.isSavingProfile(true);
            
            let formData = new FormData();
            formData.append('username', self.username());
            formData.append('bio', self.profileBio() || '');
            
            if (self.iconFile()) {
                formData.append('icon', self.iconFile(), 'icon.jpg');
            }
            formData.append('remove_icon', self.removeIconFlag() ? '1' : '0');

            fetch('/api/update_settings', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    self.showAlert("Profile changes saved successfully!", "success");
                } else {
                    self.showAlert("Error: " + (data.error || "Failed to update profile"), "error");
                }
            })
            .catch(error => {
                self.showAlert("Network error occurred.", "error");
            })
            .finally(() => {
                self.isSavingProfile(false);
            });
        };

        // パスワード変更処理
        self.handlePasswordReset = function() {
            self.isSavingPassword(true);

            fetch('/api/reset_password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ password: self.newPassword() })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    self.showAlert("Password updated successfully!", "success");
                    self.newPassword("");
                } else {
                    self.showAlert("Error: " + (data.error || "Failed to update password"), "error");
                }
            })
            .catch(error => {
                self.showAlert("Network error occurred.", "error");
            })
            .finally(() => {
                self.isSavingPassword(false);
            });
        };

        // アカウント削除処理
        self.handleDeleteAccount = function() {
            let confirmed = confirm("Are you sure you want to delete your account? This action cannot be undone and all your playlists will be lost.");
            
            if (confirmed) {
                fetch('/api/delete_account', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Your account has been deleted.");
                        window.location.href = '/';
                    } else {
                        self.showAlert("Failed to delete account.", "error");
                    }
                })
                .catch(error => {
                    self.showAlert("Network error occurred.", "error");
                });
            }
        };
    }

    ko.applyBindings(new UserSettingsViewModel(), document.getElementById('settings-page'));
</script>

</body>
</html>