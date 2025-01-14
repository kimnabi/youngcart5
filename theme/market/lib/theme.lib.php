<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 상품리스트에서 옵션항목
function get_list_options($it_id, $subject, $no)
{
    global $g5;

    if(!$it_id || !$subject)
        return '';

    $sql = " select * from {$g5['g5_shop_item_option_table']} where io_type = '0' and it_id = '$it_id' and io_use = '1' order by io_no asc ";
    $result = sql_query($sql);
    if(!sql_num_rows($result))
        return '';

    $str = '';
    $subj = explode(',', $subject);
    $subj_count = count($subj);

    if($subj_count > 1) {
        $options = array();

        // 옵션항목 배열에 저장
        for($i=0; $row=sql_fetch_array($result); $i++) {
            $opt_id = explode(chr(30), $row['io_id']);

            for($k=0; $k<$subj_count; $k++) {
                if(!is_array($options[$k]))
                    $options[$k] = array();

                if($opt_id[$k] && !in_array($opt_id[$k], $options[$k]))
                    $options[$k][] = $opt_id[$k];
            }
        }

        // 옵션선택목록 만들기
        for($i=0; $i<$subj_count; $i++) {
            $opt = $options[$i];
            $opt_count = count($opt);
            $disabled = '';
            if($opt_count) {
                $seq = $no.'_'.($i + 1);
                if($i > 0)
                    $disabled = ' disabled="disabled"';

                $str .= '<label for="it_option_'.$seq.'">'.$subj[$i].'</label>'.PHP_EOL;

                $select = '<select id="it_option_'.$seq.'" class="it_option"'.$disabled.'>'.PHP_EOL;
                $select .= '<option value="">선택</option>'.PHP_EOL;
                for($k=0; $k<$opt_count; $k++) {
                    $opt_val = $opt[$k];
                    if(strlen($opt_val)) {
                        $select .= '<option value="'.$opt_val.'">'.$opt_val.'</option>'.PHP_EOL;
                    }
                }
                $select .= '</select>'.PHP_EOL;

                $str .= $select.PHP_EOL;
            }
        }
    } else {
        $str .= '<label for="it_option_1">'.$subj[0].'</label>'.PHP_EOL;

        $select = '<select id="it_option_1" class="it_option">'.PHP_EOL;
        $select .= '<option value="">선택</option>'.PHP_EOL;
        for($i=0; $row=sql_fetch_array($result); $i++) {
            if($row['io_price'] >= 0)
                $price = '&nbsp;&nbsp;+ '.number_format($row['io_price']).'php';
            else
                $price = '&nbsp;&nbsp; '.number_format($row['io_price']).'php';

            if(!$row['io_stock_qty'])
                $soldout = '&nbsp;&nbsp;[Sold out]';
            else
                $soldout = '';

            $select .= '<option value="'.$row['io_id'].','.$row['io_price'].','.$row['io_stock_qty'].'">'.$row['io_id'].$price.$soldout.'</option>'.PHP_EOL;
        }
        $select .= '</select>'.PHP_EOL;

        $str .= $select.PHP_EOL;
    }

    return $str;
}


function memo_recv_count($mb_id)
{
    global $g5;

    if(!$mb_id)
        return 0;

    $sql = " select count(*) as cnt from {$g5['memo_table']} where me_recv_mb_id = '$mb_id' and me_read_datetime = '0000-00-00 00:00:00' ";
    $row = sql_fetch($sql);
    return $row['cnt'];
}
?>
