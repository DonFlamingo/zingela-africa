var loader = {
    template: '<div class="loading"><div class="backdrop"></div><div class="outter"><div class="middle"><div class="inner"><i class="loader"></i></div></div></div></div>',
    add: function($container) {
        dd( 'loader.add', $container );

        if ( $('.loading', $container).length )
            return;

        $container.css('position', 'relative');
        $container.append(loader.template);
    },
    remove: function($container) {
        dd( 'loader.remove', $container );
        $('.loading', $container).remove();
    }
};