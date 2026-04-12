<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($playlist['title'], ENT_QUOTES, 'UTF-8'); ?> - Sazanami</title>
    
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
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50">

<?php echo \View::forge('shared/header', array('user' => isset($user) ? $user : null)); ?>

<div id="playlist-detail-page" class="min-h-screen relative overflow-hidden">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <svg class="absolute w-full h-full opacity-30" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="wave-pattern-detail" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                    <path d="M0 50 Q 25 35, 50 50 T 100 50" fill="none" stroke="#5ba3c5" strokeWidth="0.5" opacity="0.3" />
                    <path d="M0 60 Q 25 45, 50 60 T 100 60" fill="none" stroke="#9fd4e0" strokeWidth="0.5" opacity="0.4" />
                    <path d="M0 70 Q 25 55, 50 70 T 100 70" fill="none" stroke="#b8dfe8" strokeWidth="0.5" opacity="0.3" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#wave-pattern-detail)" />
        </svg>
    </div>

    <main class="max-w-5xl mx-auto px-6 py-8 space-y-8 relative z-10">

        <div data-bind="visible: currentTrack" style="display: none;" class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-border shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)] transition-all animate-fadeIn">
            <div class="max-w-5xl mx-auto px-6 py-3 flex items-center justify-between gap-4">
                
                <div class="flex items-center gap-4 flex-1 min-w-0" data-bind="with: currentTrack">
                    <img data-bind="visible: $data.thumbnail_url, attr: { src: $data.thumbnail_url, alt: title }" class="w-12 h-12 rounded-md object-cover" style="display: none;" />
                        
                    <div data-bind="visible: !$data.thumbnail_url" class="w-12 h-12 rounded-md bg-gradient-to-br from-secondary to-accent flex items-center justify-center flex-shrink-0">
                        <i data-lucide="music" class="w-6 h-6 text-primary/40"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-foreground truncate" data-bind="text: title"></h4>
                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded-md bg-slate-100 border border-gray-200 text-muted-foreground" data-bind="text: platform"></span>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-2 px-2 sm:px-6">
                    <button data-bind="click: playPrev, disable: !hasPrev()" class="p-2 text-foreground hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-colors" title="前の曲へ">
                        <i data-lucide="skip-back" class="w-6 h-6 fill-current"></i>
                    </button>
                    <button data-bind="click: playNext, disable: !hasNext()" class="p-2 text-foreground hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-colors" title="次の曲へ">
                        <i data-lucide="skip-forward" class="w-6 h-6 fill-current"></i>
                    </button>
                </div>

                <div class="overflow-hidden bg-black transition-all duration-300 fixed bottom-24 right-6 w-64 sm:w-80 aspect-video shadow-2xl rounded-xl z-[60] border border-gray-700">
                    
                    <iframe data-bind="visible: embedUrl(), attr: { src: embedUrl, referrerpolicy: (currentTrack() && currentTrack().platform === 'niconico') ? 'no-referrer' : null }" class="w-full h-full absolute inset-0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    
                    <div data-bind="visible: !embedUrl()" class="w-full h-full absolute inset-0 flex items-center justify-center text-xs text-white bg-black">
                        再生できないプラットフォームです
                    </div>
                </div>

                <button data-bind="click: closePlayer" class="p-2 flex-shrink-0 text-muted-foreground hover:text-foreground transition-colors ml-auto">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-8 shadow-sm border border-border">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="w-48 h-48 rounded-2xl bg-gradient-to-br from-secondary to-accent flex items-center justify-center shadow-md flex-shrink-0 overflow-hidden">
                    <?php if (!empty($playlist['cover_image'])): ?>
                        <img src="<?php echo htmlspecialchars($playlist['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Cover Image" class=" w-full h-full object-cover">
                    <?php else: ?>
                        <i data-lucide="list-music" class="w-20 h-20 text-primary/60"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-4xl font-bold text-foreground mb-4">
                        <?php echo htmlspecialchars($playlist['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    
                    <?php if (isset($creator)): ?>
                    <a href="/users/<?php echo $creator['id']; ?>" class="inline-flex items-center justify-center md:justify-start gap-3 mb-5 hover:opacity-80 transition-opacity cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-secondary to-accent flex items-center justify-center border border-border shadow-sm overflow-hidden">
                            <?php if (!empty($creator['icon'])): ?>
                                <img src="<?php echo htmlspecialchars($creator['icon'], ENT_QUOTES, 'UTF-8'); ?>" alt="Creator Icon" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i data-lucide="user" class="w-4 h-4 text-primary"></i>
                            <?php endif; ?>
                        </div>
                        <span class="text-sm font-bold text-muted-foreground">
                            Created by <span class="text-foreground hover:underline"><?php echo htmlspecialchars($creator['username'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </span>
                    </a>
                    <?php endif; ?>
                    <p class="text-lg text-muted-foreground mb-6">
                        <?php echo htmlspecialchars($playlist['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    
                    <button class="inline-flex items-center gap-2 px-8 py-3 bg-primary hover:bg-primary/90 text-primary-foreground rounded-full shadow-md transition-all font-bold" data-bind="click: handlePlayFirst">
                        <i data-lucide="play" class="w-5 h-5 fill-current"></i>
                        <span>Play</span>
                    </button>
                    <button data-bind="click: handleShare" class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-50 text-foreground font-bold rounded-xl shadow-sm border border-border hover:shadow-md transition-all">
                        <i data-lucide="share-2" class="w-5 h-5"></i>
                                Share
                    </button>
                </div>
            </div>
        </div>

        <?php if ($is_owner): ?>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-border">
            <h3 class="text-lg font-bold text-foreground mb-4 flex items-center gap-2">
                <i data-lucide="link" class="w-5 h-5 text-primary"></i>
                Add New Track
            </h3>
            
            <div data-bind="visible: errorMessage, text: errorMessage" style="display: none;" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold"></div>

            <form data-bind="submit: handleAddTrack" class="flex gap-4">
                <input 
                    type="text" 
                    data-bind="value: newTrackUrl"
                    placeholder="YouTube, Niconico の URL をペースト..." 
                    class="flex-1 px-4 py-3 bg-slate-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                    required
                >
                <button 
                    type="submit" 
                    data-bind="disable: isAdding"
                    class="px-6 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    <span data-bind="text: isAdding() ? '追加中...' : 'Add Track'"></span>
                </button>
            </form>
        </div>
        <?php endif; ?>

        <div>
            <h3 class="text-xl mb-4 text-foreground font-bold flex items-center justify-between">
                <span>Tracks (<span data-bind="text: tracks().length"></span>)</span>
            </h3>
    
            <div data-bind="visible: tracks().length === 0" style="display: none;" class="text-center py-12 bg-white/50 rounded-2xl border border-dashed border-gray-300">
                <i data-lucide="music-4" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <p class="text-muted-foreground">URLを貼り付けて、最初の楽曲を追加しましょう。</p>
            </div>

            <div class="space-y-3" data-bind="sortableList: tracks, foreach: tracks">
                <div class="group flex items-center gap-4 bg-white p-3 pr-4 rounded-xl shadow-sm border border-border hover:shadow-md transition-all">
                    
                    <?php if ($is_owner): ?>
                    <div class="drag-handle cursor-grab active:cursor-grabbing p-1 text-gray-300 hover:text-primary transition-colors flex-shrink-0">
                        <i data-lucide="grip-vertical" class="w-5 h-5"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="relative w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 cursor-pointer" data-bind="click: $parent.handlePlayTrack">
                        
                        <img data-bind="visible: $data.thumbnail_url, attr: { src: $data.thumbnail_url, alt: title }" class="w-full h-full object-cover" style="display: none;" />
                        
                        <div data-bind="visible: !$data.thumbnail_url" class="absolute inset-0 bg-gradient-to-br from-secondary to-accent flex items-center justify-center">
                            <i data-lucide="music" class="w-8 h-8 text-primary/40"></i>
                        </div>
                        
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <i data-lucide="play" class="w-6 h-6 text-white fill-current"></i>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h4 class="text-foreground font-bold truncate mb-1" data-bind="text: title"></h4>
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            
                            <span data-bind="visible: $data.artist" style="display: none;" class="flex items-center gap-2">
                                <span class="truncate max-w-[150px]" data-bind="text: artist"></span>
                                <span class="text-gray-300">&bull;</span>
                            </span>
                            
                            <span class="px-2 py-0.5 rounded-md text-xs font-bold uppercase bg-slate-100 border border-gray-200" data-bind="text: platform"></span>
                        </div>
                    </div>

                    <?php if ($is_owner): ?>
                    <button data-bind="click: $parent.handleDeleteTrack" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100" aria-label="Delete track">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </button>
                    <?php endif ?>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
    lucide.createIcons();

    ko.bindingHandlers.sortableList = {
        init: function(element, valueAccessor, allBindings, viewModel, bindingContext) {
            let list = valueAccessor();
            let originalNextSibling; // ドラッグ開始時の「正しい位置」を記憶する変数

            Sortable.create(element, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'opacity-30',
                dragClass: 'shadow-2xl',
                
                // ドラッグ開始時に、その要素のすぐ後ろにあるノード（Knockoutのコメント等）を記憶
                onStart: function(evt) {
                    originalNextSibling = evt.item.nextSibling;
                },
                
                onEnd: function(evt) {
                    if (evt.oldIndex === evt.newIndex) return;

                    let itemEl = evt.item;

                    if (originalNextSibling) {
                        element.insertBefore(itemEl, originalNextSibling);
                    } else {
                        element.appendChild(itemEl);
                    }

                    let underlyingArray = list();
                    let item = underlyingArray[evt.oldIndex];
                    
                    underlyingArray.splice(evt.oldIndex, 1);
                    underlyingArray.splice(evt.newIndex, 0, item);
                    
                    list.valueHasMutated();

                    // サーバーへ並び順を保存
                    if (bindingContext.$data.saveTrackOrder) {
                        bindingContext.$data.saveTrackOrder();
                    }
                }
            });
        }
    };

    function PlaylistDetailViewModel() {
        let self = this;

        self.playlistId = <?php echo $playlist['id']; ?>;
        let initialTracks = <?php echo json_encode($tracks ?? array()); ?>;
        self.tracks = ko.observableArray(initialTracks);

        self.newTrackUrl = ko.observable("");
        self.errorMessage = ko.observable("");
        self.isAdding = ko.observable(false);

        self.currentTrack = ko.observable(null);

        self.currentIndex = ko.computed(function() {
            let current = self.currentTrack();
            if (!current) return -1;
            return self.tracks().indexOf(current);
        });

        self.hasNext = ko.computed(function() {
            return self.currentIndex() >= 0 && self.currentIndex() < self.tracks().length - 1;
        });

        self.hasPrev = ko.computed(function() {
            return self.currentIndex() > 0;
        });

        self.playNext = function() {
            if (self.hasNext()) {
                self.handlePlayTrack(self.tracks()[self.currentIndex() + 1]);
            }
        };

        self.playPrev = function() {
            if (self.hasPrev()) {
                self.handlePlayTrack(self.tracks()[self.currentIndex() - 1]);
            }
        };
        
        self.embedUrl = ko.computed(function() {
            let track = self.currentTrack();
            if (!track || !track.url) return null;

            let url = track.url;
            let platform = track.platform;

            // 1. YouTube の場合
            if (platform === 'youtube' || url.indexOf('youtu') !== -1) {
                let ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})/);
                if (ytMatch && ytMatch[1]) {
                    // autoplay=1 を付けて自動再生させる
                    return "https://www.youtube.com/embed/" + ytMatch[1] + "?autoplay=1";
                }
            }
            
            // 2. ニコニコ動画 の場合
            else if (platform === 'niconico' || url.indexOf('nico') !== -1) {
                let nicoMatch = url.match(/(?:nicovideo\.jp\/watch\/|nico\.ms\/)(sm[0-9]+|nm[0-9]+|so[0-9]+|[a-zA-Z0-9]+)/);
                if (nicoMatch && nicoMatch[1]) {
                    // TODO: 自動再生
                    return "https://embed.nicovideo.jp/watch/" + nicoMatch[1];
                }
            }

            return null;
        });

        self.handlePlayTrack = function(track) {
            self.currentTrack(track);
            setTimeout(function() { lucide.createIcons(); }, 10);
        };

        self.closePlayer = function() {
            self.currentTrack(null);
        };

        self.handlePlayFirst = function() {
            if (self.tracks().length > 0) {
                self.handlePlayTrack(self.tracks()[0]);
            } else {
                alert("再生する楽曲がありません。");
            }
        };

        self.handleAddTrack = function() {
            let url = self.newTrackUrl();
            if (!url) return;

            self.errorMessage("");
            self.isAdding(true);

            fetch('/api/add_track', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.csrfToken },
                body: JSON.stringify({
                    playlist_id: self.playlistId,
                    url: url
                })
            })
            .then(response => response.json())
            .then(data => {
                self.isAdding(false);
                if (data.error) {
                    self.errorMessage(data.error);
                } else if (data.success && data.track) {
                    self.tracks.unshift(data.track);
                    self.newTrackUrl("");
                    setTimeout(() => lucide.createIcons(), 10);
                }
            })
            .catch(error => {
                self.isAdding(false);
                self.errorMessage("通信エラーが発生しました。");
            });
        };

        self.handleDeleteTrack = function(track) {
            if (!confirm('「' + track.title + '」をプレイリストから削除しますか？')) return;
            
            fetch('/api/delete_track', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.csrfToken },
                body: JSON.stringify({ pt_id: track.pt_id }) 
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('エラー: ' + data.error);
                } else if (data.success) {
                    self.tracks.remove(track);
                }
            })
            .catch(error => {
                alert('通信エラーが発生しました。');
            });
        };

        self.handleShare = function() {
            // 現在開いているページのURLを取得
            let url = window.location.href;
            
            // クリップボードAPIを使用してコピー
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(function() {
                    alert("プレイリストのリンクをクリップボードにコピーしました！\n" + url);
                }).catch(function(err) {
                    alert("コピーに失敗しました。URLバーから直接コピーしてください。");
                    console.error('Failed to copy: ', err);
                });
            } else {
                alert("このブラウザでは自動コピーができません。\n以下のURLを手動でコピーしてください：\n\n" + url);
            }
        };

        self.saveTrackOrder = function() {
            let orderedIds = self.tracks().map(function(track) {
                return track.pt_id; 
            });

            fetch('/api/reorder_tracks', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.csrfToken },
                body: JSON.stringify({
                    playlist_id: self.playlistId,
                    track_ids: orderedIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('順番の保存に失敗しました: ' + data.error);
                }
            })
            .catch(error => {
                console.error('通信エラーが発生しました。', error);
            });
        };
    }

    ko.applyBindings(new PlaylistDetailViewModel(), document.getElementById('playlist-detail-page'));
</script>

</body>
</html>