<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Playlists - Sazanami</title>
    
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
                        border: '#e5e7eb'
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

<div id="user-page" class="min-h-screen relative overflow-hidden">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <svg class="absolute w-full h-full opacity-30" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="wave-pattern-user" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                    <path d="M0 50 Q 25 35, 50 50 T 100 50" fill="none" stroke="#5ba3c5" strokeWidth="0.5" opacity="0.3" />
                    <path d="M0 60 Q 25 45, 50 60 T 100 60" fill="none" stroke="#9fd4e0" strokeWidth="0.5" opacity="0.4" />
                    <path d="M0 70 Q 25 55, 50 70 T 100 70" fill="none" stroke="#b8dfe8" strokeWidth="0.5" opacity="0.3" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#wave-pattern-user)" />
        </svg>
    </div>

    <main class="max-w-7xl mx-auto px-6 py-8 relative z-10">
        <div class="bg-gradient-to-br from-secondary/40 via-white to-accent/30 rounded-3xl p-8 shadow-lg border border-border mb-8">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="flex-shrink-0">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-secondary to-accent flex items-center justify-center border-4 border-white shadow-lg overflow-hidden">
                            
                            <?php if (!empty($user['icon'])): ?>
                                <img src="<?php echo htmlspecialchars($user['icon'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Icon" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i data-lucide="user" class="w-16 h-16 text-primary"></i>
                            <?php endif; ?>

                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-primary text-primary-foreground rounded-full p-2 shadow-md">
                            <i data-lucide="music" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-3xl text-foreground font-bold mb-2">
                        <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>
                    </h1>
                    <p class="text-muted-foreground mb-4">
                        <?php echo htmlspecialchars($user['bio'] ?? '自己紹介がまだありません', ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <div class="flex flex-wrap gap-6 justify-center md:justify-start text-sm">
                        <div>
                            <span class="text-2xl text-primary font-bold block" data-bind="text: playlists().length">0</span>
                            <span class="text-muted-foreground">Playlists</span>
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0 flex gap-3">
                    <a href="/playlists/settings" class="p-4 bg-white hover:bg-secondary/50 text-primary rounded-full shadow-md hover:shadow-lg transition-all flex items-center justify-center" aria-label="Settings">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                    </a>
                    <button data-bind="click: handleCreateNew" class="inline-flex items-center gap-2 px-6 py-4 bg-primary hover:bg-primary/90 text-primary-foreground rounded-full shadow-lg hover:shadow-xl transition-all font-bold">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        <span>Create New Playlist</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl text-foreground font-bold mb-6">My Playlists</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-bind="foreach: playlists">
                <div class="bg-white rounded-2xl p-4 shadow-md hover:shadow-xl border border-border transition-all cursor-pointer">
                    <a data-bind="attr: { href: '/playlists/view/' + id }" class="block relative overflow-hidden rounded-xl mb-4 aspect-square bg-gray-100 group">
                        
                        <img data-bind="visible: $data.cover_image, attr: { src: $data.cover_image, alt: title }" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" style="display: none;" />
                            
                        <div data-bind="visible: !$data.cover_image" class="absolute inset-0 bg-gradient-to-br from-secondary/40 to-accent/60 flex items-center justify-center group-hover:from-secondary/60 group-hover:to-accent/80 transition-all">
                            <i data-lucide="list-music" class="w-20 h-20 text-primary/60"></i>
                        </div>
                    </a>

                    <div class="flex items-start justify-between gap-3">
                        <a data-bind="attr: { href: '/playlists/view/' + id }" class="flex-1 min-w-0 block">
                            <h3 class="text-lg text-foreground font-bold truncate mb-1" data-bind="text: title"></h3>
                            <p class="text-sm text-muted-foreground truncate" data-bind="text: description"></p>
                        </a>

                        <button data-bind="click: $parent.handleEdit" class="p-2 rounded-full hover:bg-secondary/50 text-muted-foreground hover:text-primary transition-colors" aria-label="Edit playlist">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </button>
                        <button data-bind="click: $parent.handleDeletePlaylist, clickBubble: false" class="p-2 text-muted-foreground hover:text-red-500 hover:bg-red-50 rounded-full transition-colors" aria-label="Delete playlist">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                        
                    </div>
                </div>
            </div>
            
            <div data-bind="visible: playlists().length === 0" class="text-center py-12 text-muted-foreground" style="display: none;">
                <p>プレイリストがまだありません。「Create New Playlist」から作成してみましょう！</p>
            </div>
        </div>
    </main>

    <div data-bind="visible: showModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        
        <div data-bind="click: closeModal" class="absolute inset-0"></div>
        
        <div class="w-full max-w-lg bg-white rounded-3xl p-10 shadow-2xl border border-border relative z-10 transition-all transform scale-100">
            
            <button data-bind="click: closeModal" class="absolute top-6 right-6 text-muted-foreground hover:text-foreground transition-colors" aria-label="Close modal">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>

            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-foreground" data-bind="text: modalMode() === 'create' ? 'Create New Playlist' : 'Edit Playlist'"></h2>
            </div>
            
            <form data-bind="submit: savePlaylist" class="space-y-6">
                
                <div>
                    <label for="playlist-name" class="block text-sm font-bold mb-2 text-foreground">Playlist Title<span class="text-red-500 ml-1">*</span></label>
                    <input 
                        id="playlist-name" 
                        type="text" 
                        data-bind="value: modalTitle"
                        placeholder="e.g. Chill Summer Beats" 
                        required 
                        class="w-full px-5 py-4 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-muted-foreground"
                    >
                </div>
                
                <div>
                    <label for="playlist-description" class="block text-sm font-bold mb-2 text-foreground">Description</label>
                    <textarea 
                        id="playlist-description" 
                        data-bind="value: modalDescription"
                        placeholder="Add a short description about this playlist..." 
                        rows="5" 
                        class="w-full px-5 py-4 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-muted-foreground resize-none"
                    ></textarea>
                </div>

                <div>
                    <label for="playlist-description" class="block text-sm font-bold mb-2 text-foreground">Cover Image</label>
                    <div class="flex items-center gap-6">
                        <div class="relative w-32 h-32 rounded-2xl overflow-hidden group cursor-pointer border-2 transition-all flex-shrink-0"
                             onclick="document.getElementById('cover-image-upload').click()"
                             data-bind="css: { 'border-dashed border-primary/30 hover:border-primary': !coverImagePreview(), 'border-white shadow-md': coverImagePreview() }">

                            <div class="w-full h-full bg-gradient-to-br from-secondary/40 to-accent/60 flex items-center justify-center"
                                 data-bind="visible: !coverImagePreview()">
                                <div class="text-center">
                                    <i data-lucide="image-plus" class="w-8 h-8 text-primary/60 mx-auto mb-1"></i>
                                    <span class="text-xs text-primary/60 font-bold">Upload</span>
                                </div>
                            </div>

                            <div class="absolute inset-0 w-full h-full bg-slate-100"
                                 data-bind="visible: coverImagePreview()">
                                <img data-bind="attr: { src: coverImagePreview }" class="w-full h-full object-cover" alt="" />
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-lucide="camera" class="w-8 h-8 text-white"></i>
                                </div>
                            </div>

                            <input type="file" id="cover-image-upload" accept="image/*" class="hidden" data-bind="event: { change: handleImageUpload }">
                        </div>
                        
                        <div class="flex-1">
                            <p class="text-sm text-muted-foreground mb-3">
                                Recommended: Square image, at least 400x400px JPG, PNG or GIF. Max size 2MB.
                            </p>
                            <button type="button" data-bind="visible: coverImagePreview, click: removeCoverImage" style="display: none;" class="text-sm font-bold text-red-500 hover:text-red-600 transition-colors flex items-center gap-1">
                                <i data-lucide="trash-2" class="w-4 h-4"></i> 画像を削除
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button 
                        type="submit" 
                        data-bind="disable: isSaving, text: modalMode() === 'create' ? 'Create Playlist' : 'Save Changes'"
                        class="w-full py-4 bg-primary hover:bg-primary/90 text-primary-foreground font-bold rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Create Playlist
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script>
    // ページ読み込み後にLucideアイコンを描画
    lucide.createIcons();

    function UserPageViewModel() {
        var self = this;

        // PHPから渡された既存のプレイリストデータをObservableArrayに格納
        var initialPlaylists = <?php echo json_encode($playlists ?? array()); ?>;
        self.playlists = ko.observableArray(initialPlaylists);

        // モーダルの状態管理
        self.showModal = ko.observable(false);
        self.modalMode = ko.observable("create"); // "create" or "edit"
        self.editingId = ko.observable(null);
        
        // フォーム入力用のデータバインド変数
        self.modalTitle = ko.observable("");
        self.modalDescription = ko.observable("");

        self.isSaving = ko.observable(false);

        self.coverImagePreview = ko.observable(null); // プレビュー用のBase64データ
        self.coverImageFile = ko.observable(null);    // 送信用のFileオブジェクト

        self.removeCoverFlag = ko.observable(false);

        // 新規作成ボタンが押された時
        self.handleCreateNew = function() {
            self.modalMode("create");
            self.editingId(null);
            self.modalTitle("");
            self.modalDescription("");
            self.removeCoverFlag(false);
            self.removeCoverImage();
            self.showModal(true);
        };

        // 編集ボタンが押された時
        self.handleEdit = function(playlist) {
            self.modalMode("edit");
            self.editingId(playlist.id);
            self.modalTitle(playlist.title);
            self.modalDescription(playlist.description);
            self.coverImagePreview(playlist.cover_image || null);
            self.coverImageFile(null);
            self.removeCoverFlag(false);
            self.showModal(true);
        };

        self.handleImageUpload = function(data, event) {
            var file = event.target.files[0];
            if (!file) return;

            // ファイルが画像かどうかチェック
            if (!file.type.match('image.*')) {
                alert("画像ファイルを選択してください。");
                event.target.value = "";
                return;
            }
            
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.onload = function() {
                    // ▼ 自動リサイズの最大サイズを設定 (例: 800x800)
                    var MAX_WIDTH = 800;
                    var MAX_HEIGHT = 800;
                    var width = img.width;
                    var height = img.height;

                    // 縦横比を維持したまま縮小計算
                    if (width > height) {
                        if (width > MAX_WIDTH) {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                    } else {
                        if (height > MAX_HEIGHT) {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                    }

                    // Canvasを作って画像をリサイズ描画
                    var canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    // CanvasからDataURL (Base64) を取得 (JPEG形式で画質を80%に圧縮)
                    var dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                    
                    // プレビューにセット
                    self.coverImagePreview(dataUrl);

                    // ▼ Base64を「送信用のBlob（ファイル）データ」に変換する処理
                    var bin = atob(dataUrl.split(',')[1]);
                    var buffer = new Uint8Array(bin.length);
                    for (var i = 0; i < bin.length; i++) {
                        buffer[i] = bin.charCodeAt(i);
                    }
                    var blob = new Blob([buffer.buffer], {type: 'image/jpeg'});
                    
                    self.coverImageFile(blob); // 圧縮済みのデータをセット
                    self.removeCoverFlag(false);
                    
                    setTimeout(function() { lucide.createIcons(); }, 10);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        };

        self.removeCoverImage = function() {
            self.coverImagePreview(null);
            self.coverImageFile(null);
            self.removeCoverFlag(true);
            var fileInput = document.getElementById('cover-image-upload');
            if (fileInput) fileInput.value = ""; 
        };

        // モーダルを閉じる
        self.closeModal = function() {
            self.showModal(false);
        };

        // フォーム送信（Ajax通信）
        self.savePlaylist = function() {
            if (!self.modalTitle()) {
                alert("プレイリスト名を入力してください");
                return;
            }

            self.isSaving(true);

            // ファイルを送信するため、FormData オブジェクトを使用する
            var formData = new FormData();
            formData.append('title', self.modalTitle());
            formData.append('description', self.modalDescription() || '');
            
            if (self.modalMode() === 'edit') {
                formData.append('id', self.editingId());
            }

            // 画像ファイルが選択されていればFormDataに追加
            if (self.coverImageFile()) {
                // ▼ 第3引数に仮想のファイル名 ('cover.jpg') を追加します
                formData.append('cover_image', self.coverImageFile(), 'cover.jpg');
            }

            formData.append('remove_cover', self.removeCoverFlag() ? '1' : '0');

            fetch('/api/save_playlist', {
                method: 'POST',
                // ※注意: FormDataを送信する場合、Content-Typeヘッダーは指定してはいけません（ブラウザが自動設定します）
                body: formData
            })
            .then(response => {
                return response.text(); // 一旦JSONではなく単なるテキストとして受け取る
            })
            .then(text => {
                try {
                    // 受け取ったテキストをJSONに変換してみる
                    var data = JSON.parse(text);
                    if (data.error) {
                        alert('エラー: ' + data.error);
                    } else if (data.success) {
                        window.location.reload(); // 成功時はリロード
                    }
                } catch (e) {
                    // JSON変換に失敗した＝FuelPHPがHTMLのエラー画面などを吐いている！
                    console.error("【サーバーからのエラー応答】\n", text);
                    alert("サーバー側でエラーが発生しました。F12キーを押して「Console」タブの赤い文字を確認してください。");
                }
            })
            .catch(error => {
                console.error("ネットワークエラー:", error);
                alert("通信が切断されました。");
            })
            .finally(() => {
                self.isSaving(false);
            });
        };

        // ▼ 追加: プレイリストの削除 ▼
        self.handleDeletePlaylist = function(playlist) {
            if (!confirm('本当にプレイリスト「' + playlist.title + '」を削除しますか？\n（追加された楽曲もリストから消去されます）')) {
                return;
            }

            fetch('/api/delete_playlist', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: playlist.id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('エラー: ' + data.error);
                } else if (data.success) {
                    // UI上から消す
                    self.playlists.remove(playlist);
                }
            })
            .catch(error => {
                alert('通信エラーが発生しました。');
            });
        };
    }

    // ViewModelの適用
    ko.applyBindings(new UserPageViewModel(), document.getElementById('user-page'));
</script>

</body>
</html>