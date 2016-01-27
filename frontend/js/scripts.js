(function($){

    /*
     * Front-end Funcionalities
     * - Listeners and AJAX callings to server
     */

    $(document).on('ready', function () {
        var $entityList = $('#entity-list');
        var $url = $entityList.data('urladdtag');
        var $modal = $('#myModal');

        $modal.find('.addTagAction').on('click', function(e) {
            e.preventDefault();
            $modal.modal('toggle');
            $tag = $(this).html()
            $that = $(this)
            $.ajax(
                $url,
                {
                    'method': 'POST',
                    'dataType': 'json',
                    'data': {
                        '_method': 'POST',
                        'id': $modal.data('id'),
                        'tag': $tag
                    },
                    'beforeSend': function () {
                        return true;
                    },
                    'success': function (dataRes) {
                        //add tag to this line...
                        console.log(dataRes.id);
                        $entityList.find('#'+dataRes.id).find('.field-concept').append(dataRes.tag)

                    },
                    'error': function (err) {
                        console.log('error');
                    }
                }
            )
        })

        $entityList.find('.addTagBtn').on('click', function (e) {
            e.preventDefault();
            var entityId = $(this).closest('[data-id]').data('id');
            $modal.data('id', entityId )
        });
    })

})(jQuery);