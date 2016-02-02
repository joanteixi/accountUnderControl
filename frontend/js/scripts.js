(function ($) {

    /*
     * Front-end Funcionalities
     * - Listeners and AJAX callings to server
     */


    updateLine = function updateLine(dataRes, lineId) {
        $(lineId).find('.field-concept').append(dataRes.tag)
    }

    function postTag($url, $id, $tag, callback) {
        $.ajax(
            $url,
            {
                'method': 'POST',
                'dataType': 'json',
                'data': {
                    '_method': 'POST',
                    'id': $id,
                    'tag': $tag
                },
                'beforeSend': function () {
                    return true;
                },
                'success': function (dataRes) {
                    //add tag to this line...
                    console.log(callback);
                    callback(dataRes, '#' + dataRes.id);
                    //console.log(dataRes.id);
                    //$entityList.find('#'+dataRes.id).find('.field-concept').append(dataRes.tag)

                },
                'error': function (err) {
                    console.log('error');
                }
            }
        )
    }

    $(document).on('ready', function () {
        var $entityList = $('#entity-list');
        var $url = $entityList.data('urladdtag');
        var $modal = $('#myModal');

        $modal.find('.addTagAction').on('click', function (e) {
            e.preventDefault();
            $modal.modal('toggle');
            $tag = $(this).html()
            $that = $(this)
            postTag($url, $modal.data('id'), $tag, updateLine);
            //function POST
        })

        //add new tag
        $modal.find('#addNewTag').keyup(function (e) {
            if (e.keyCode == 13) {
                $modal.modal('toggle');
                postTag($url, $modal.data('id'), $(this).val(), updateLine);
            }
        });

        //open modal
        $entityList.find('.addTagBtn').on('click', function (e) {
            e.preventDefault();
            var entityId = $(this).closest('[data-id]').data('id');
            $modal.data('id', entityId)
        });
    })

})(jQuery);