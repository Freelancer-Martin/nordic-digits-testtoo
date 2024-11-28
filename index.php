<!doctype html>
<html lang="en">
<head>
  <title>Testtoo</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/redmond/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/jquery-ui.min.js"></script>
</head>

<body>
  <style>
  .pagination-container
  {

    text-align: center;
    position: relative;
    margin: 10px;
    padding: 10px;

  }

  .popup {
        width:100%;
        height:100%;
        display:none;
        position:fixed;
        top:0px;
        left:0px;
        background:rgba(0,0,0,0.75);
      }

      /* Inner */
      .popup-inner {
        max-width:700px;
        width:90%;
        padding:40px;
        position:absolute;
        top:50%;
        left:50%;
        -webkit-transform:translate(-50%, -50%);
        transform:translate(-50%, -50%);
        box-shadow:0px 2px 6px rgba(0,0,0,1);
        border-radius:3px;
        background:#fff;
      }

      /* Close Button */
      .popup-close {
        width:30px;
        height:30px;
        padding-top:4px;
        display:inline-block;
        position:absolute;
        top:0px;
        right:0px;
        transition:ease 0.25s all;
        -webkit-transform:translate(50%, -50%);
        transform:translate(50%, -50%);
        border-radius:1000px;
        background:rgba(0,0,0,0.8);
        font-family:Arial, Sans-Serif;
        font-size:20px;
        text-align:center;
        line-height:100%;
        color:#fff;
      }

      .popup-close:hover {
        -webkit-transform:translate(50%, -50%) rotate(180deg);
        transform:translate(50%, -50%) rotate(180deg);
        background:rgba(0,0,0,1);
        text-decoration:none;
      }
  </style>

  <div class="col-lg-12"   >

    <?php

    $actual_link_full = "http://{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
    $int = (int) filter_var( $actual_link_full, FILTER_SANITIZE_NUMBER_INT);


    $url = sprintf( 'https://api.nordic-digital.com/v1/product?&page=%s&sort=created_at&per_page=50"', $int  );

    $data = array( '','' );

    $query_url = sprintf("%s?%s", $url, http_build_query($data));
    $curl = curl_init();


    $password = '';
    $headers = array(
        'Authorization: Basic '. base64_encode("SI2I66HTB6SW9ZD7B6GN4FM4WBADPTER:$password")
    );

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, $query_url );
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($curl, CURLOPT_TIMEOUT, 10 );
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10 );
    curl_setopt($curl, CURLOPT_HEADER, 0 );
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1 );
    $http_code = curl_getinfo($curl , CURLINFO_HTTP_CODE );
    $result = curl_exec($curl);


    curl_close($curl);


    function pagination( $numberpage, $page_start, $page_end, $next_page, $prev_page, $currentPage )
    {

      print '<div class="pagination-container" >';
      $NUMPERPAGE = $numberpage;
      $this_page = "/testtoo";
      $data = range( $page_start, $page_end );
      $num_results = count( $data );



      if(!isset($_GET['page']) || !$page = intval($_GET['page'])) {
        $page = 1;
      }


      $linkextra = [];
      if(isset($_GET['var1']) && $var1 = $_GET['var1']) {
        $linkextra[] = "var1=" . urlencode($var1);
      }
      $linkextra = implode("&amp;", $linkextra);
      if($linkextra) {
        $linkextra .= "&amp;";
      }


      $tmp = [];
      for($p=1, $i=0; $i < $num_results; $p++, $i += $NUMPERPAGE) {
        if($page == $p) {

          $tmp[] = "<b>{$p}</b>";
        } else {
          $tmp[] = "<a href=\"{$this_page}?{$linkextra}page={$p}\">{$p}</a>";
        }
      }


      for($i = count($tmp) - 3; $i > 1; $i--) {
        if(abs($page - $i - 1) > 2) {
          unset($tmp[$i]);
        }
      }

      // display page navigation iff data covers more than one page
      if(count($tmp) > 1) {
        echo "<p>";

        if($page > 1) {
          // display 'Prev' link
          echo "<a href=\"{$this_page}?{$linkextra}page=" . ($page - 1) . "\">&laquo; Prev</a> | ";
        } else {
          echo "Page ";
        }

        $lastlink = 0;
        foreach($tmp as $i => $link) {
          if($i > $lastlink + 1) {
            echo " ... "; // where one or more links have been omitted
          } elseif($i) {
            echo " | ";
          }
          echo $link;
          $lastlink = $i;
        }

        if($page <= $lastlink) {
          // display 'Next' link
          echo " | <a href=\"{$this_page}?{$linkextra}page=" . ($page + 1) . "\">Next &raquo;</a>";
        }

        echo "</p>\n\n";
      }

       print '<div>';

    }
    // end of function

    ?>

    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Nimi</th>
          <th scope="col">Tootekood</th>
          <th scope="col">EAN</th>
          <th scope="col">Tootja</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $decode = json_decode( $result );
          $paging_array = $decode->resource;

          foreach( $decode->products as $key => $product  )
          {

            $html = '';
            $html .= '<div class="popup" data-popup="popup-'.$key.'">';
            	$html .= '<div class="popup-inner">';
                $html .= '<h4>Nimi: '.$product->name->et.'</h4>';
            		$html .= '<h4>Ese Nr: '.$product->id.'</h4>';
                $html .= '<h4 class="insert-price"  >Hind: </h4>';
                $html .= '<h4 class="insert-quantaty"  >Kogus: </h4>';
            		$html .= '<div class="insert-images"></div>';
            		$html .= '<p><a data-popup-close="popup-'.$key.'" href="#">Close</a></p>';
            		$html .= '<a class="popup-close" data-popup-close="popup-'.$key.'" href="#">x</a>';
            	$html .= '</div>';
            $html .= '</div>';

            $html .= '<tr onclick="loadDoc( '.$product->id.' )" data-popup-open="popup-'.$key.'" href="#" >';

                if( ! empty( $product->barcodes ) )
                {
                  $html .= '<th scope="row">'.$key.'</th>';
                }

                if( ! empty( $product->name->et ) )
                {
                  $html .= '<td>'.$product->name->et.'</td>';
                }

                if( ! empty( $product->reference ) )
                {
                  $html .= '<td>'.$product->reference.'</td>';
                }

                if( ! empty( $product->barcodes ) )
                {
                  $html .= '<td>'.$product->barcodes[0].'</td>';
                }

                if( ! empty( $product->barcodes ) )
                {
                  $html .= '<td>'.$product->brand->name.'</td>';
                }

              $html .= '</tr>';

              print $html;

          }

      ?>
      </tbody>
    </table>
    <?php
      pagination(
                  $paging_array->paging->maxPerPage,
                  1,
                  $paging_array->total,
                  $paging_array->paging->_links->next,
                  $paging_array->paging->_links->last,
                  $paging_array->paging->currentPage
                );
     ?>



    </div>
  </body>
</html>




<script type="text/javascript">

    $(function() {
    	//----- OPEN
    	$('[data-popup-open]').on('click', function(e) {
    		var targeted_popup_class = jQuery(this).attr('data-popup-open');
    		$('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);

    		e.preventDefault();
    	});

    	//----- CLOSE
    	$('[data-popup-close]').on('click', function(e) {
    		var targeted_popup_class = jQuery(this).attr('data-popup-close');
    		$('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

    		e.preventDefault();
    	});
    });





    //API Stock and resource request
    function loadDoc( ItemId ) {

      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          var result = JSON.parse(xhr.response);
          $('.insert-price').text( 'HIND: ' + result.stock[0].prices.rrp + '  Eur' );
          $('.insert-quantaty').text( 'KOGUS: ' + result.stock[0].quantity + '  TK' );
          console.log( result.stock );

        }
      }
      xhr.open( 'GET', 'https://api.nordic-digital.com/v1/product/'+ItemId+'/stock', true );
      xhr.withCredentials = true;
      xhr.send();
//=============================================================================

      var xhrresources = new XMLHttpRequest();
      xhrresources.onreadystatechange = function() {
        if (xhrresources.readyState === 4) {


          var results = JSON.parse(xhrresources.response);
          var images = results.resources.images;

          var out = "";
          var i;
          for(i = 0; i < images.length; i++) {
            out += '<img src="' + images[i].url + '" height="200" width="200"  >';
          }

          $('.insert-images').html(out);
        }
      }
      xhrresources.open( 'GET', 'https://api.nordic-digital.com/v1/product/'+ItemId+'/resources', true );
      xhrresources.withCredentials = true;
      xhrresources.send();


    }

</script>
