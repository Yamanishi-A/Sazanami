<template id="playlist-card-template">
    <a data-bind="attr: { href: '/playlists/view/' + id }" class="group block cursor-pointer h-full">
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-border/50 hover:border-primary/30 h-full flex flex-col relative">
            
            <div class="aspect-square w-full overflow-hidden relative bg-gray-100">
                <img data-bind="visible: coverImage, attr: { src: coverImage, alt: title }" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" style="display: none;" />
                <div data-bind="visible: !coverImage" class="absolute inset-0 bg-gradient-to-br from-secondary/40 to-accent/60 flex items-center justify-center group-hover:from-secondary/60 group-hover:to-accent/80 transition-all">
                    <i data-lucide="list-music" class="w-16 h-16 text-primary/60"></i>
                </div>
            </div>

            <div class="p-4 space-y-3 flex flex-col flex-1">
                <h3 class="text-lg font-semibold text-foreground line-clamp-2 group-hover:text-primary transition-colors" data-bind="text: title"></h3>
                
                <div class="flex items-center gap-2 mt-auto pt-2">
                    <div class="w-6 h-6 rounded-full overflow-hidden bg-secondary flex-shrink-0 border border-gray-200">
                        <img data-bind="visible: creatorAvatar, attr: { src: creatorAvatar }" class="w-full h-full object-cover" />
                        <div data-bind="visible: !creatorAvatar" class="w-full h-full flex items-center justify-center to-accent text-primary bg-gradient-to-br from-secondary">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        </div>
                    <span class="text-sm font-medium text-muted-foreground truncate" data-bind="text: creatorName"></span>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-border/50">
                    <div class="flex items-center gap-1 text-sm font-bold text-muted-foreground">
                        <i data-lucide="music" class="w-4 h-4"></i>
                        <span data-bind="text: trackCount + ' tracks'"></span>
                    </div>
                    
                    <div class="flex items-center gap-1" data-bind="foreach: platforms">
                        <div data-bind="html: $component.getPlatformIcon($data)"></div>
                    </div>
                </div>
            </div>
        </div>
    </a>
</template>

<script>
// 重複登録を防ぐガード
if (!ko.components.isRegistered('playlist-card')) {
    ko.components.register('playlist-card', {
        viewModel: function(params) {
            var self = this;
            
            // 親(foreach)から渡されたデータをコンポーネント自身の変数にセット
            self.id = params.id;
            self.title = params.title;
            self.coverImage = params.coverImage;
            self.creatorName = params.creatorName;
            self.creatorAvatar = params.creatorAvatar;
            self.trackCount = params.trackCount;
            self.platforms = params.platforms;

            // スクリプトブロックにあったアイコン生成ロジックをここに完全カプセル化！
            self.getPlatformIcon = function(platform) {
                var colorClass = '';
                var svg = '';
                switch(platform) {
                    case 'youtube':
                        colorClass = 'text-red-500';
                        svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
                        break;
                    case 'spotify':
                        colorClass = 'text-[#1DB954]';
                        svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.84.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>';
                        break;
                    case 'niconico':
                        colorClass = 'text-[#231815]';
                        svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M.4 0v24h23.2V0H.4zm4.8 4.8h4.8v14.4H5.2V4.8zm9.6 0h4.8v14.4h-4.8V4.8z"/></svg>';
                        break;
                    default:
                        colorClass = 'text-gray-500';
                        svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M9 18V5l12-2v13M9 9v9"/></svg>';
                }
                return '<span class="' + colorClass + '" title="' + platform + '">' + svg + '</span>';
            };
        },
        template: { element: 'playlist-card-template' }
    });
}
</script>