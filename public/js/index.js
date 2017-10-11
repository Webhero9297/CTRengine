var aggregation_values = [0.01, 0.05, 0.1, 0.5, 1, 2.5, 5, 10];
var aggregation_num = 4;
var order_trade_selection = 'order';
var open_fill_selection = 'open';
var front_asset = 'BTC', back_asset = 'ETH';
var order_type, order_side;
var flag;
$(document).ready(function(){
    
    initial_css();
    set_asset();  
    get_tradeprice();

    $( ".header .product_selection h4" ).click(
        function(){
            $(".header .marketinfo .menu_entry").css('color','rgba(81,141,202,.8)');
        },
        function(){
        }
    );
    $( ".header .product_selection h4" ).click(
        function() {
            if ($( ".header .product_list" ).css('display') == 'none'){
                $( ".header .product_list" ).css('display', 'block');
            } else {
                $( ".header .product_list" ).css('display', 'none');
            }
        }
    );
    $(".sidebar .orderform .market").click(function(){
        $(".sidebar .orderform .market").css('border','1px solid #fff');
        $(".sidebar .orderform .market").css('color','#fff');
        
        $(".sidebar .orderform .limit").css('border','0px');
        $(".sidebar .orderform .limit").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .stop").css('border','0px');
        $(".sidebar .orderform .stop").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .stoplimit").css('border','0px');
        $(".sidebar .orderform .stoplimit").css('color','hsla(0,0%,100%,.4)');

        $(".sidebar .orderform .market_order").css('display','block');
        $(".sidebar .orderform .limit_order").css('display','none');
        $(".sidebar .orderform .stop_order").css('display','none');
        $(".sidebar .orderform .stoplimit_order").css('display','none');
    });
    $(".sidebar .orderform .limit").click(function(){
        $(".sidebar .orderform .market").css('border','0px');
        $(".sidebar .orderform .market").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .limit").css('border','1px solid #fff');
        $(".sidebar .orderform .limit").css('color','#fff');
        
        $(".sidebar .orderform .stop").css('border','0px');
        $(".sidebar .orderform .stop").css('color','hsla(0,0%,100%,.4)');

        $(".sidebar .orderform .stoplimit").css('border','0px');
        $(".sidebar .orderform .stoplimit").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .market_order").css('display','none');
        $(".sidebar .orderform .limit_order").css('display','block');
        $(".sidebar .orderform .stop_order").css('display','none');
        $(".sidebar .orderform .stoplimit_order").css('display','none');
    });
    $(".sidebar .orderform .stop").click(function(){
        $(".sidebar .orderform .market").css('border','0px');
        $(".sidebar .orderform .market").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .limit").css('border','0px');
        $(".sidebar .orderform .limit").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .stop").css('border','1px solid #fff');
        $(".sidebar .orderform .stop").css('color','#fff');
        
        $(".sidebar .orderform .stoplimit").css('border','0px');
        $(".sidebar .orderform .stoplimit").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .market_order").css('display','none');
        $(".sidebar .orderform .limit_order").css('display','none');
        $(".sidebar .orderform .stop_order").css('display','block');
        $(".sidebar .orderform .stoplimit_order").css('display','none');
    });
    $(".sidebar .orderform .stoplimit").click(function(){
        $(".sidebar .orderform .market").css('border','0px');
        $(".sidebar .orderform .market").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .limit").css('border','0px');
        $(".sidebar .orderform .limit").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .stop").css('border','0px');
        $(".sidebar .orderform .stop").css('color','hsla(0,0%,100%,.4)');
        
        $(".sidebar .orderform .stoplimit").css('border','1px solid #fff');
        $(".sidebar .orderform .stoplimit").css('color','#fff');

        $(".sidebar .orderform .market_order").css('display','none');
        $(".sidebar .orderform .limit_order").css('display','none');
        $(".sidebar .orderform .stop_order").css('display','none');
        $(".sidebar .orderform .stoplimit_order").css('display','block');
    });

    $(".sidebar .orderform .buy").click(function(){
        $(".sidebar .orderform .buy").css('color','#fff');
        $(".sidebar .orderform .buy").css('border','2px solid #31ff31');
        $(".sidebar .orderform .buy").css('font-weight','bold');
        
        $(".sidebar .orderform .sell").css('color','hsla(0,0%,100%,.5)');
        $(".sidebar .orderform .sell").css('border','1px solid hsla(0,0%,100%,.5)');
        $(".sidebar .orderform .sell").css('font-weight','normal');
        $(".sidebar .orderform .stateful_btn").css('border','2px solid #31ff31');
        $(".sidebar .orderform .stateful_btn").html("PLACE BUY ORDER");

        set_asset();
    });
    $(".sidebar .orderform .sell").click(function(){
        $(".sidebar .orderform .buy").css('color','hsla(0,0%,100%,.5)');
        $(".sidebar .orderform .buy").css('border','1px solid hsla(0,0%,100%,.5)');
        $(".sidebar .orderform .buy").css('font-weight','normal');
        
        $(".sidebar .orderform .sell").css('color','#fff');
        $(".sidebar .orderform .sell").css('border','2px solid #fd2d2f');
        $(".sidebar .orderform .sell").css('font-weight','bold');
        $(".sidebar .orderform .stateful_btn").css('border','2px solid #fd2d2f');
        $(".sidebar .orderform .stateful_btn").html("PLACE SELL ORDER");

        set_reverse_asset();
    });
    
    $(".stateful_btn").click(function(){
        var  quantity, price, stop_price = '0', limit_price, expiration_date, time_in_force;
        get_orderstate();
        if (order_type == "market") {
            price = $('.market_stat .num').html();
            if ($('.market_order .amount').val() == ''){
                alert("input amount");
                return;
            }
            if (order_side == "buy") {
                quantity = parseFloat($('.market_order .amount').val()) / parseFloat(price);
            } else if (order_side == "sell") {
                quantity = $('.market_order .amount').val();
            }
        } else if (order_type == "limit") {
            price = $('.limit_order .limit_price').val();
            quantity = $('.limit_order .amount').val();
            if ($('.limit_order .amount').val() == ''){
                alert("input amount");
                return;
            }
            if ($('.limit_order .limit_price').val() == ''){
                alert("input limit price");
                return;
            }
        } else if (order_type == "stop") {
            price = $('.market_stat .num').html();
            stop_price = $(".stop_order .stop_price").val();
            limit_price = 'NONE';
            if ($('.stop_order .amount').val() == ''){
                alert("input amount");
                return;
            }
            if ($('.stop_price').val() == ''){
                alert("input stop price");
                return;
            }
            if (order_side == "buy") {
                quantity = parseFloat($('.stop_order .amount').val()) / parseFloat(price);
            } else if (order_side == "sell") {
                quantity = $('.stop_order .amount').val();
            }
        } else if (order_type == "stoplimit") {
            price = $('.market_stat .num').html();
            stop_price = $(".stoplimit_order .stop_price").val();
            limit_price = $('.stoplimit_order .limit_price').val();
            if ($('.stoplimit_order .amount').val() == ''){
                alert("input amount");
                return;
            }
            if ($('.stop_price').val() == ''){
                alert("input stop price");
                return;
            }
            if (order_side == "buy") {
                quantity = parseFloat($('.stoplimit_order .amount').val()) / parseFloat(price);
            } else if (order_side == "sell") {
                quantity = $('.stoplimit_order .amount').val();
            }
        }
        time_in_force = $('#case').val();
        if (time_in_force == "DAY") {
            expiration_date = 1;
        } else if (time_in_force == "GTDT") {
            expiration_date = date_format($('#calendar').val(), $('#cancel_time').val());
        }

        var post_param = {
            customer_id: 1,
            order_side: order_side,
            order_type: order_type,
            quantity: quantity,
            price: parseFloat(price),
            stop_price: stop_price,
            limit_price: limit_price,
            time_in_force: time_in_force,
            expiration_date: expiration_date,
            want_asset: front_asset,
            offer_asset: back_asset
        };
        $.post('addorder', post_param, function(resp) {
            console.log(resp);
        });
    });

    $(".sidebar .orderform .advanced_btn").click(function(){
        if ($(".sidebar .orderform .advanced_content").css('display') == 'none'){
            $(".sidebar .orderform .advanced_content").css('display','block');
            $(".sidebar .orderform .advanced_section .header span").css('transform','rotate(0deg)');
        }
        else {
            $(".sidebar .orderform .advanced_content").css('display','none');
            $(".sidebar .orderform .advanced_section .header span").css('transform','rotate(-90deg)');
        }
    });
        
    $(".order_tab").click(function(){
        order_trade_selection = 'order';
        $(".order_tab").css('color','#fff');
        $(".order_tab").css('border-bottom','1px solid #fff');
        $(".trade_tab").css('color','hsla(206,8%,82%,.6)');
        $(".trade_tab").css('border-bottom','hsla(206,8%,82%,.6)');
        
        $(".order_book_panel").css('display','flex');
        $(".trade_history_panel").css('display','none');
    });
    $(".trade_tab").click(function(){
        order_trade_selection = 'trade';
        $(".trade_tab").css('color','#fff');
        $(".trade_tab").css('border-bottom','1px solid #fff');
        $(".order_tab").css('color','hsla(206,8%,82%,.6)');
        $(".order_tab").css('border-bottom','hsla(206,8%,82%,.6)');
        
        $(".order_book_panel").css('display','none');
        $(".trade_history_panel").css('display','flex');
    });
    
    $(".open_tab").click(function(){
        open_fill_selection = 'open';
        $(".open_tab").css('color','#fff');
        $(".open_tab").css('border-bottom','1px solid #fff');
        $(".fill_tab").css('color','hsla(206,8%,82%,.6)');
        $(".fill_tab").css('border-bottom','hsla(206,8%,82%,.6)');
        
        $(".open_orders_panel").css('display','flex');
        $(".fills_panel").css('display','none');
    });
    $(".fill_tab").click(function(){
        open_fill_selection = 'fill';
        $(".fill_tab").css('color','#fff');
        $(".fill_tab").css('border-bottom','1px solid #fff');
        $(".open_tab").css('color','hsla(206,8%,82%,.6)');
        $(".open_tab").css('border-bottom','hsla(206,8%,82%,.6)');
        
        $(".open_orders_panel").css('display','none');
        $(".fills_panel").css('display','flex');
    });
    
    $('.asset_item').click(function(){
        $('.asset_section').css('display','none');
        $( ".c-nav" ).animate({width: 0}, 300);
        var str = $(this).find('.crypto_name').html();
        var res = str.split("/");
        front_asset = res[0];
        back_asset = res[1];

        set_asset();
        init_asset_balance();
    });
    
    $('.aggregation_dec').click(function(){
        if (aggregation_num > 0)    {
            aggregation_num--;
            $('.aggregation .value').html(parseFloat(aggregation_values[aggregation_num]).toFixed(2));
        }
    });
    $('.aggregation_inc').click(function(){
        if (aggregation_num < 7)    {
            aggregation_num++;
            $('.aggregation .value').html(parseFloat(aggregation_values[aggregation_num]).toFixed(2));
        }
    });
    
    $('.navicon').click(function(){
        
        if ($('.c-nav').width() > 0){
            $('.asset_section').css('display','none');
            $( ".c-nav" ).animate({width: 0}, 300);
        } else {
            $( ".c-nav" ).animate({
                width: 228,
            }, 300, function() {
                $('.asset_section').css('display','block');
            });
        }
    });
    $('.trade_btn').click(function(){
        hide_all();
        toDefaultColor();
        $('.trade_btn').css('color','white');
        $('.sidebar').css('display','flex');
    });
    $('.book_btn').click(function(){
        hide_all();
        toDefaultColor();
        $('.book_btn').css('color','white');
        $('.middle_panel').css('display','flex');
        $('.order_book_panel' ).css('display','flex');
    });
    $('.charts_btn').click(function(){
        hide_all();
        toDefaultColor();
        $('.charts_btn').css('color','white');
        $('.middle_panel').css('display','flex');
        $('.price_chart_panel').css('display','flex');
    });
    $('.orders_btn').click(function(){
        hide_all();
        toDefaultColor();
        $('.orders_btn').css('color','white');
        $('.bottom_panel').css('display','flex');
        $('.open_orders_panel').css('display','flex');
    });
    
    $('#case').on('change', function() {
        if (this.value  == "GTDT"){
            $(".section.cancel").css("display","block");
        } else {
            $(".section.cancel").css("display","none");
        }
    })

    $( ".market_order .amount" ).keyup(function() {
        calc_total();
    });
    $( ".limit_order .amount" ).keyup(function() {
        calc_total();
    });
    $( ".limit_order .limit_price" ).keyup(function() {
        calc_total();
    });

    $("#deposit_btn").click(function(){
        /**
         * Changed By Webhero9297
         */
        var want_asset = (parseFloat($(".front_currency_price").html()) == 0 ) ? '' : $(".front_currency_price").html();
        var offer_asset = (parseFloat($(".back_currency_price").html()) == 0 ) ? '' : $(".back_currency_price").html();
        $("#front_asset_value").addClass('text-right');
        $("#back_asset_value").addClass('text-right');
        $("#front_asset_value").val(want_asset);
        $("#back_asset_value").val(offer_asset);
        /*** End */
        $(".deposit_bg").css('display','block');

        flag = "deposit";
    })
    $("#withdraw_btn").click(function(){
        var want_asset = (parseFloat($(".front_currency_price").html()) == 0 ) ? '' : $(".front_currency_price").html();
        var offer_asset = (parseFloat($(".back_currency_price").html()) == 0 ) ? '' : $(".back_currency_price").html();
        $("#front_asset_value").addClass('text-right');
        $("#back_asset_value").addClass('text-right');
        $("#front_asset_value").val(want_asset);
        $("#back_asset_value").val(offer_asset);
        $(".deposit_bg").css('display','block');

        flag = "withdraw";
    });  
    $("#btn_deposit_cancel").click(function(){
        $(".deposit_bg").css('display','none');
    });
    $("#btn_deposit_ok").click(function(){
        /**
         * Changed By Webhero9297
         */
        var want_asset_amount = $("#front_asset_value").val();
        var offer_asset_amount = $("#back_asset_value").val();
        var product = front_asset + "-" + back_asset;
        var post_param = { want_asset_amount:want_asset_amount, offer_asset_amount:offer_asset_amount };
        if (flag == "deposit") {
            $.post('assetdeposit/'+ product, post_param, function(resp) {
                init_asset_balance();
            });
        } else {
            $.post('assetwithdraw/'+ product, post_param, function(resp) {
                init_asset_balance();
            });
        }
        /*** End */
        $(".deposit_bg").css('display','none');

    });

    init_asset_balance();
    init_time();

});

$(window).resize(function(){
    initial_css();
    
    if ($('body').width() > 1473){
        $(".order_book_panel").css('display','flex');
        $(".trade_history_panel").css('display','flex');
        $(".open_orders_panel").css('display','flex');
        $(".fills_panel").css('display','flex');
    } else {
        if ($('body').width() >= 750){
            show_all();
            if (order_trade_selection == 'order'){
                $(".order_book_panel").css('display','flex');
                $(".trade_history_panel").css('display','none');
            } else {
                $(".order_book_panel").css('display','none');
                $(".trade_history_panel").css('display','flex');
            }
            if (open_fill_selection == 'open'){
                $(".open_orders_panel").css('display','flex');
                $(".fills_panel").css('display','none');
            } else {
                $(".open_orders_panel").css('display','none');
                $(".fills_panel").css('display','flex');
            }
        }
    }
});

function init_time() {
    var items = new Array();
    for (var i=0;i<48;i++){
        if (i % 2 == 0){
            items[i] = i / 2 + ':00';
        } else {
            items[i] = Math.floor(i / 2) + ':30';
        }
    }
    $.each(items, function (i, item) {
        $('#cancel_time').append($('<option>', { 
            value: item,
            text : item 
        }));
    });
}

function date_format(date_str, time_str) {
    var tmp = date_str.split('-');
    var result = tmp[2] + '-' + tmp[1] + '-' + tmp[0] + ' ' + time_str + ':00';
    return result;
}

function init_asset_balance() {

    $.get('getassetbalance/'+ front_asset + '-' + back_asset, function(resp) {
        resp = JSON.parse(resp);
        $('.front_currency_price').html(resp.want_asset_amount);
        $('.back_currency_price').html(resp.offer_asset_amount);
    });
}

function doOnERC20Toggle() {
    if ($('#toggle_erc20').is(':checked')) {
        $('.content').css('display', 'none');
        $('.etherdelta').css('display', 'block');
    } else {
        $('.content').css('display', 'block');
        $('.etherdelta').css('display', 'none');
    }
}

function toDefaultColor(){
    $('.trade_btn').css('color','hsla(0,0%,100%,.5)');
    $('.book_btn').css('color','hsla(0,0%,100%,.5)');
    $('.charts_btn').css('color','hsla(0,0%,100%,.5)');
    $('.orders_btn').css('color','hsla(0,0%,100%,.5)');
}

function initial_css(){
    if ($('body').width() < 750){
        cnav_height = $('body').height() - 86;
        sidebar_height = $('body').height() - 46;
        middle_panel_width = $('body').width();
        order_book_panel_height = $('body').height() - 46;
        order_book_content_height = order_book_panel_height - 77;
        price_chart_content_height = order_book_panel_height - 67;
        price_chart_panel_height = order_book_panel_height;
        trade_history_content_height = order_book_panel_height - 72;
        price_chart_panel_width = $('body').width();
        fills_content_height = $('body').height() - $('.open_orders_header').height() - $('.open_orders_panel .table_head').height() - 46;
        bottom_panel_height = $('body').height() - 46;
    } else{
        if ($('body').width() > 1473){
            order_book_panel_height = 347;
            price_chart_panel_height = 696;
            price_chart_content_height = 618;
            price_chart_panel_width = $('body').width() - 230 - 620 - 4;
        } else {
            order_book_panel_height = 347;
            price_chart_panel_height = 347;
            price_chart_content_height = 590;
            price_chart_panel_width = $('body').width() - 232;
        }
        bottom_panel_height = $('body').height() - 696;
        cnav_height = $('body').height() - 40;
        sidebar_height = $('body').height();
        middle_panel_width = $('body').width() - 232;
        trade_history_content_height = order_book_panel_height - 72;
        fills_content_height = bottom_panel_height -72;
        order_book_content_height = order_book_panel_height - 58;
    }
    etherdelta_height = $('body').height() - 30;
    $(".c-nav").css("height", cnav_height + 'px');
    $(".sidebar").css("height", sidebar_height + 'px');
    $(".middle_panel").css("width", middle_panel_width + 'px');
    $(".bottom_panel").css("width", middle_panel_width + 'px');
    $(".bottom_panel").css("height", bottom_panel_height+ 'px');
    $(".price_chart_panel").css("width", price_chart_panel_width + 'px');
    $(".price_chart_panel").css("height", price_chart_panel_height + 'px');
    $(".price_chart_content").css('height',price_chart_content_height + 'px');
    $(".fills_panel .table_content").css("height", fills_content_height + 'px');
    $(".open_orders_panel").css("width", price_chart_panel_width + 'px');
    $(".open_orders_panel .table_content").css("height", fills_content_height + 'px');
    $(".order_book_panel").css('height',order_book_panel_height + 'px');
    $(".order_book_panel .table_content").css('height', order_book_content_height + 'px');
    $(".trade_history_panel").css('height',order_book_panel_height + 'px');
    $(".trade_history_panel .table_content").css('height', trade_history_content_height + 'px');

    $('.etherdelta iframe').attr('width', '100%');
    $('.etherdelta iframe').attr('height', etherdelta_height + 'px');
}

function hide_all(){
    $('.sidebar').css('display','none');
    $('.bottom_panel').css('display','none');
    $('.middle_panel').css('display','none');
    $('.price_chart_panel').css('display','none');
    $('.trade_history_panel' ).css('display','none');
    $('.order_book_panel' ).css('display','none');
    $('.open_orders_panel' ).css('display','none');
    $('.fills_panel' ).css('display','none');
}

function show_all(){
    $('.sidebar').css('display','flex');
    $('.bottom_panel').css('display','flex');
    $('.middle_panel').css('display','flex');
    $('.price_chart_panel').css('display','flex');
    $('.trade_history_panel' ).css('display','flex');
    $('.order_book_panel' ).css('display','flex');
    $('.open_orders_panel' ).css('display','flex');
    $('.fills_panel' ).css('display','flex');
}

function set_asset(){
    $('.front_asset').html(front_asset);
    $('.back_asset').html(back_asset);
    var sel_type = $('#sel_type').val();
    var sel_graphType = $('#sel_chart').val();		
    var market_type = 'BTC-USD';
    requestData(sel_type, sel_graphType, back_asset, market_type);
}

function set_reverse_asset(){
    $('.market_order .back_asset').html(front_asset);
    $('.market_order .order_total .front_asset').html(back_asset);

    $('.stop_order .amount_section .back_asset').html(front_asset);
    $('.stop_order .order_total .front_asset').html(back_asset);
}

function get_tradeprice(){
    $.get('gettradeprice/' + front_asset + '-' + back_asset, function(resp) {
        resp = JSON.parse(resp);
        $('.market_stat .num').html(parseFloat(resp.result.trade_price).toFixed(8));
    });   
}

function calc_total(){
    get_orderstate();
    if (order_type == "market"){
        var amount = parseFloat($('.market_order .amount').val() || 0);
        var tradeprice = parseFloat($('.market_stat .num').html());
        var total;
        if (order_side == "buy"){
            if (tradeprice != 0)
                total = parseFloat(amount / tradeprice).toFixed(8);
        } else {
            total = parseFloat(amount * tradeprice).toFixed(8);
        }
        $('.market_order .total').html(total);
    } else if (order_type == "limit") {
        var amount = parseFloat($('.limit_order .amount').val() || 0);
        var limitprice = parseFloat($('.limit_order .limit_price').val() || 0);
        var total = parseFloat(amount * limitprice).toFixed(2);
        $('.limit_order .total').html(total);
    }
}

function get_orderstate() {
    if ($('.market').css('color') == 'rgb(255, 255, 255)' || $('.market').css('color') == '#fff') {
        order_type = "market";
    } else if ($('.limit').css('color') == 'rgb(255, 255, 255)' || $('.limit').css('color') == '#fff') {
        order_type = "limit";
    } else if ($('.stop').css('color') == 'rgb(255, 255, 255)' || $('.stop').css('color') == '#fff') {
        order_type = "stop";
    } else if ($('.stoplimit').css('color') == 'rgb(255, 255, 255)' || $('.stop').css('color') == '#fff') {
        order_type = "stoplimit";
    }
    
    if ($('.buy').css('color') == 'rgb(255, 255, 255)' || $('.buy').css('color') == '#fff') {
        order_side = "buy";
    } else if ($('.sell').css('color') == 'rgb(255, 255, 255)' || $('.sell').css('color') == '#fff') {
        order_side = "sell";
    }
}

// Chart Part
$(document).ready(function(){
  
      var d = new Date(); console.log(d);
      var end = new Date(d.setTime(d.getTime() + (0*60*60*1000))); // now time
      var start = new Date(d.setTime(d.getTime() - (1*60*60*1000))); // before 1 hours
      var sel_type = 60; sel_graphType = 'candlestick';
      var market_type = "BTC-USD"; 
      $('#sel_type').change(function(){	
          sel_type = $('#sel_type').val();
          requestData(sel_type, sel_graphType, back_asset, market_type);
                  
      });
      $('#sel_chart').change(function(){	
          sel_graphType = $('#sel_chart').val();		
          requestData(sel_type, sel_graphType, back_asset, market_type);
      });

      setInterval(function(){ requestData(sel_type, sel_graphType, back_asset, market_type); }, 3000);
      setInterval(function(){ draw_chart_order(back_asset, market_type); }, 3000);
});