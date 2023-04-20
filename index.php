<?php
    $limit = 10;
    $query = isset($_REQUEST['q']) ? $_REQUEST['q']: false;
    $results = false;
    $engine_name = "SearchIt!!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body, .container-fluid
        {
            border: 0px;
            margin: 0px;
            padding: 0px;
        }
        .content{
            justify-content: center;
            padding-top: 12%;
            padding-left: 30%;
            padding-right: 30%;
        }
        .name{
            padding: 0px 0px 20px 0px;
            font-size: 70px;
            text-align: center;
            color: #3361FF;
        }
        .search_box{
            padding: 20px 30% 20px 30%;
            font-size: 20px;
        }
        #q{
            border-radius: 25px;
            width: 100%;
            font-size: 25px;
            border: 1px solid #d1d5e0;
            padding-left: 25px;
        }
        .radio{
            text-align: center;
            padding-bottom: 20px;
            line-height: 1.58;
            font-family: arial,sans-serif;
            font-size: 17px;
        }
        #submit{
            width: 150px;
            padding: 5px 5px;
            margin: 0px 17px;
            border-radius: 25px;
            margin-left: 35%;
            background-color: #f8f9fa;
            border: 1px solid #f8f9fa;
            border-radius: 4px;
            color: #3c4043;
            font-family: Roboto,arial,sans-serif;
            font-size: 18px;
        }
        .results-head{
            padding-top: 30px;
            padding-left: 5%;
            padding-right: 5%;
            padding-bottom: 10px;
        }
        .results-title a{
            font-size: 25px;
            text-align: left;
            color: #FF5233;
            text-decoration: none;
        }
        .results-query{
            font-size: 20px;
            text-align: left;
        }
        .results-summary{
            border-top: 0.5px solid #70757a;
            padding-left: 5%;
            padding-right: 5%;
            padding-top: 10px;
            font-size: 12px;
            font-family: sans-serif, Arial, Helvetica;
            color: #70757a;
        }
        .results-block{
            padding-left: 10%;
            padding-right: 10%;
            padding-top: 20px;
            padding-bottom: 90px;
        }
        .results-record{
            padding-top: 20px;
            padding-bottom: 20px;
        }
        #results-urls{
            font-size: 14px;
            color: #202124;
            text-decoration: none;
        }
        #results-title{
            font-family: arial,sans-serif;
            font-size: 20px;
            text-decoration: None;
        }
        #results-id{
            color: #4d5156;
            line-height: 1.58;
            font-family: arial,sans-serif;
            font-size: 14px;
        }
        #results-description{
            color: #4d5156;
            line-height: 1.58;
            font-family: arial,sans-serif;
            font-size: 17px;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <title>Search!</title>

</head>
<body>
<?php
    if(!$query){
        ?>
        <div class="contianer-fluid">
            <div class="row">
                <div class="col-12 content">
                    <div class="name">
                        <?php echo $engine_name;?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="search_box">
                        <form accept-charset="utf-8" method="get">
                            <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($query, ENT_QUOTES, "utf-8");?>">
                            <br><br>
                            <div class="radio">
                                <input type="radio" id="rad_lucene" name="algorithm" value="lucene" /> Solr's Lucene
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" id="rad_page_rank" name="algorithm" value="pagerank" /> PageRank
                            </div>
                            <input type="submit" value="Search" id="submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    else{
        require_once('solr-php-client-master/Apache/Solr/Service.php');
        $solr = new Apache_Solr_Service('localhost', 8983, '/solr/hw4');
        if(get_magic_quotes_gpc() == 1){
            $query = stripslashes($query);
        }
        try{
            if(!isset($_GET['algorithm']))
                $_GET['algorithm'] = "lucene";
            if($_GET['algorithm'] == "lucene")
                $results = $solr->search($query, 0, $limit);
            else{
                $param = array('sort'=>'pageRankFile desc');
                $results = $solr->search($query, 0, $limit, $param);
            }
        }
        catch (Exception $e){
            die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
        }
        if($results){
            $total = (int)$results->response->numFound;
            $start = min(1, $total);
            $end = min($limit, $total);
            ?>
            <div class="container-fluid">
                <div class="row results-head">
                    <div class="col-md-4 hidden-sm">
                        <div class="results-title">
                            <a href="index.php"><?php echo $engine_name;?></a>
                        </div>
                    </div>
                    <div class="col-md-8 col-12 results-query">
                        Search Query: <?php echo $query;?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Algorithm: <?php echo $_GET['algorithm'];?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="results-summary">
                            Showing <?php echo $end ?> out of <?php echo $total ?> results
                        </div>
                        <div class="d-flex flex-column bd-highlight mb-3 results-block">
                            <?php
                                foreach($results->response->docs as $doc){
                                    $title = $doc->title;
                                    $url = $doc->og_url;
                                    $id = $doc->id;
                                    $description = $doc->og_description;
                                    if($title == "" || $title == null)
                                        $title = "N/A";
                                    $csv_latimes = array_map('str_getcsv', file('URLtoHTML_latimes_news.csv'));  
                                    if($url == "" || $url == null){
                                        foreach($csv_latimes as $record){
                                            $temp = "/Users/paras/Desktop/solr-7.7.3/server/solr/LATIMES/latimes/".$record[0];
                                            if ($id == $temp) {
                                                $url = $record[1];
                                                unset($record);
                                                break;
                                            }
                                        }
                                    }
                                    if($description == "" || $description == null)
                                        $description = "N/A";  
                                    ?>
                                    <div class="bd-highlight results-record">
                                        <a href="<?php echo $url;?>" id="results-urls" target="_blank"><?php echo $url."<br>";?></a>
                                        <a href="<?php echo $url;?>" id="results-title" target="_blank"><?php echo $title."<br>";?></a>
                                        <span id="results-id"><?php echo $id."<br>";?></span>
                                        <span id="results-description"><?php echo $description."<br>";?></span>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
    

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>
</body>
</html>