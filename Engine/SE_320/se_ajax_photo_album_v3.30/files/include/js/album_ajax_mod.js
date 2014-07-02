function clearTag()
    {
    $('media_tags').style.display = 'none';
    $('media_tags').getElements("[id^='full_tag_']").each(function(item, index)
        {
        if (item)
            {
            item.destroy();
            }
        });

    $('media_photo_div').getElements("[id^='tag_']").each(function(item, index)
        {
        if (item && item.id != 'tag_save' && item.id != 'tag_cancel' && item.id != 'tag_me')
            {
            item.destroy();
            }
        });
    }

function nextpic(album, pic, user, profile)
    {
    if (( typeof profile == "undefined") || (profile == 'false') || (profile == false))
        {
        profile = false;
        url = 'album_file.php';
        }

    else
        {
        url = 'profile_photos_file.php';
        }

    if (block_val == false)
        {
        block_val = true;
        var pagest = $('carousel_page_num').get('text');
        var current_value = $('post_comment_box').getElement('input[name=value]').value;
        var loading = $('loading');
        var pic_src = $('media_photo');
        var media_photo_div = $('media_photo_div');

        var request = new Request.JSON({ method: 'post',
                                         url: url,
                                         data: { 'task': 'ajax', 'media_id': pic, 'album_id': album, 'user': user,
                                                 'pagest': pagest
        },                               onComplete: function(responseObject)
            {
            if ($type(responseObject) != "object" || !responseObject.result || responseObject.result == "failure")
                {
                alert('ERR');
                }

            else
                {
                height = media_photo_div.getStyle("height");
                width = media_photo_div.getStyle("width");
                photo_div_tmp = $('photo_div_' + responseObject.media_id);
                photo_div_tmp_cur = $('photo_div_' + current_value);

                if (responseObject.media_id != current_value)
                    {
                    photo_div_tmp_cur.setStyle('display', 'none');

                    if (responseObject.media_width > 608)
                        {
                        media_width_caption = responseObject.media_width + 'px';
                        }

                    else
                        {
                        media_width_caption = 608 + 'px';
                        }

                    myEffect_div = new Fx.Morph('media_photo_div',
                                                { duration: 600, transition: Fx.Transitions.Sine.easeOut
                    });

                    myEffect_caption = new Fx.Morph('media_caption',
                                                    { duration: 600, transition: Fx.Transitions.Sine.easeOut
                    });

                    myEffect_div.start({ 'height': responseObject.media_height, //Morphs the 'height' style from 10px to 100px.
                                         'width': responseObject.media_width
                                             + 'px'                             //Morphs the 'width' style from 900px to 300px.
                    });

                    var media_caption = $('media_caption');

                    myEffect_caption.start({ 'width': responseObject.media_caption //Morphs the 'width' style from 900px to 300px.
                    });
                    }

                if (photo_div_tmp == null)
                    {
                    loading.setStyle('display', 'block');
                    var newElementVar = new Element('div',
                                                    { 'id': 'photo_div_' + responseObject.media_id,
                                                      'styles': { 'position': 'absolute', 'opacity': 1
                    //   'height': 0, //Morphs the 'height' style from 10px to 100px.
                    //   'width': 0
                    }
                    });

                    new Asset.image(responseObject.img_src,
                                    { id: 'media_photo_' + responseObject.media_id, onload: function(img)
                        {
                        img.inject(newElementVar);

                        newElementVar.inject(photo_div_tmp_cur, 'before');

                        new_img_effect(responseObject,    pic_src, img,                 current_value, newElementVar,
                                       photo_div_tmp_cur, loading, media_width_caption, profile);
                        }
                    });
                    }

                else
                    {
                    if (responseObject.media_id != current_value)
                        {
                        photo_div_tmp.setStyle('display', 'block');
                        photo_div_tmp.inject($('photo_div_' + current_value), 'before');
                        media_photo_tmp = $('media_photo_' + responseObject.media_id);

                        media_photo_tmp.setStyle('opacity', 1);
                        new_img_effect(responseObject,    pic_src, media_photo_tmp,     current_value, photo_div_tmp,
                                       photo_div_tmp_cur, loading, media_width_caption, profile);
                        }

                    else
                        block_val = false;
                    }
                }
            }
        }).send();
        }
    }

function new_img_effect(responseObject,      pic_src, img, current_value, photo_div_tmp, photo_div_tmp_cur, loading,
                        media_width_caption, profile)
    {
    var last = $('last');
    var next = $('next');
    var posted_date = $('posted_date');
    var current_num = $('current_num').innerHTML;
    var direct_link = $('direct_link');
    var embedded_image = $('embedded_image');
    var text_link = $('text_link');
    var ubb_code = $('ubb_code');
    var report_content = $('report_content');

    var media_caption = $('media_caption');
    var current_value = $('post_comment_box').getElement('input[name=value]').value;

    var postcomment = $('media_' + current_value + '_postcomment');
    var comments = $('media_' + current_value + '_comments');

    loading.setStyle('display', 'none');
    var media_photo = $('media_photo_' + responseObject.media_id);
    media_photo.setStyle('height', responseObject.media_height);
    media_photo.setStyle('width', responseObject.media_width);
    photo_div_tmp.inject(photo_div_tmp_cur, 'after');

    pic_src.set('id', 'media_photo_' + current_value);
    img.set('id', 'media_photo');

    last.onclick = function(event)
        {
        nextpic(responseObject.album_id, responseObject.last, responseObject.user_name, profile)
        };

    next.onclick = function(event)
        {
        nextpic(responseObject.album_id, responseObject.next, responseObject.user_name, profile)
        };

    temp = current_num.split('#');
    num = temp[1].indexOf(' ');
    $('current_num').innerHTML = temp[0] + '#' + responseObject.current_num + temp[1].substring(num);

    if (profile)
        {
        $('from').innerHTML = responseObject.from;
        }

    posted_date.innerHTML = posted_date.innerHTML.split(' ')[0] + ' ' + responseObject.posted_date;
    direct_link.innerHTML = responseObject.direct_link;
    embedded_image.set('text', responseObject.embedded_image);
    text_link.set('text', responseObject.text_link);
    ubb_code.set('text', responseObject.ubb_code);
    report_content.href = responseObject.report_content;
    var title = $('title');
    var desc = $('desc');
    if (responseObject.title == "" || responseObject.title == null) {
       title.setStyle('display', 'none'); 
    }
    else {
       title.setStyle('display', 'block');
       title.set('text', responseObject.title);     
    }
    if (responseObject.desc == ""  || responseObject.desc == null) {
       desc.setStyle('display', 'none'); 
    }
    else {
       desc.setStyle('display', 'block');
       desc.set('text', responseObject.desc);     
    }

    asd = responseObject.current_index;
    current_id = parseInt(responseObject.current_index - 2);

    clearTag();
    SocialEngine.MediaTag.options.media_id = responseObject.media_id;
    SocialEngine.MediaTag.options.media_dir = responseObject.media_dir;

    if (profile)
        {
        SocialEngine.Owner.ImportUserInfo({ "user_exists": true,
                                            "user_id": responseObject.user_owner.user_id,
                                            "user_username": responseObject.user_owner.user_username,
                                            "user_fname": responseObject.user_owner.user_fname,
                                            "user_lname": responseObject.user_owner.user_lname,
                                            "user_subnet_id": responseObject.user_owner.user_subnet_id,
                                            "user_status": responseObject.user_owner.user_status,
                                            "user_photo": responseObject.user_owner.user_photo
        });
        }

    SocialEngine.MediaTag.tags = [];

    dir = responseObject.media_dir;

    responseObject.tags.each(function(item, index)
        {
        SocialEngine.MediaTag.insertTag(item['tag_id'], item['tag_link'],  item['tag_text'],   item['tag_x'],
                                        item['tag_y'],  item['tag_width'], item['tag_height'], item['tagged_user']);
        });

    $('media_' + current_value + '_totalcomments').innerHTML = responseObject.initialTotal;
    $('media_' + current_value + '_totalcomments').id = 'media_' + responseObject.media_id + '_totalcomments';
    comments.innerHTML = '';
    postcomment.id = 'media_' + responseObject.media_id + '_postcomment';
    comments.id = 'media_' + responseObject.media_id + '_comments';
    $('post_comment_box').getElement('input[name=value]').value = responseObject.media_id;
    $('confirmcommentdelete').getElement('input[name=value]').value = responseObject.media_id;

    SocialEngine.MediaComments.options.typeID = responseObject.media_id;
    SocialEngine.MediaComments.options.initialTotal = responseObject.initialTotal;
    SocialEngine.MediaComments.options.cpp = responseObject.initialTotal;
    SocialEngine.MediaComments.total = responseObject.initialTotal;
    SocialEngine.MediaComments.getComments();
    block_val = false;
    }

function album_carousel(page, owner_id, album_id)
    {
    var request = new Request.JSON({ method: 'post', url: 'album_carousel.php',
                                     data: { 'type': 'ajax', 'owner_id': owner_id, 'album_id': album_id, 'page': page
    },                               onComplete: function(responseObject)
        {
        if ($type(responseObject) != "object" || !responseObject.result || responseObject.result == "failure")
            {
            alert('ERR');
            }

        else
            {
            }
        }
    });
    }

function updateCarousel(direction, album_id, user_id, profile)
    {
    if (typeof profile == "undefined")
        profile = false;

    var page_num = 1;
    var total = parseInt($('carousel_count').get('text'));

    if (direction == "next")
        {
        page_num = parseInt($('carousel_page_num').get('text')) + 1;
        }

    else if (direction == "prev")
        {
        page_num = parseInt($('carousel_page_num').get('text')) - 1;
        }

    else
        {
        alert("!!!");
        }

    if (page_num < 1)
        {
        page_num = total;
        }

    if (page_num > total)
        {
        page_num = 1;
        }

    $('carousel_page_num').set('text', page_num);

    temp = new Request.HTML({ method: 'get',            url: 'album_ajax_carousel.php', data: { 'do': '1'
    },                        update: 'album_carousel', onRequest: function()
        {
        $('album_carousel').fade('0')
        },                    onComplete: function()
        {
        $('album_carousel').fade('1')
        }
    }).send('p=' + page_num + '&album_id=' + album_id + '&user_id=' + user_id + '&profile=' + profile);
    }