<?php
include_once("dataLayer.php");

session_start();
if(isset($_COOKIE['asp-token']) && isset($_COOKIE['asp-user-id'])){

    $dl = new dataLayer();
    $validCookie = $dl->tokenAuth($_COOKIE['asp-token'], $_COOKIE['asp-user-id']);

    if($validCookie == true){
        
    }else{
        header("Location:index.php");
    }

}else{
    header("Location:index.php");
}

?>

<html> 
    <link href="https://fonts.googleapis.com/css?family=Julius+Sans+One|Nanum+Pen+Script|Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Julius+Sans+One|Nanum+Pen+Script|Nunito" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet"></link>
    <link href="styles.css" rel="stylesheet"></link>
    
    <head>
        <title>Alpha Sigma Phi - Create Report</title>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    </head>
    
    
    <body>
    
    <div id="outer-border"> 
    
        
        <div class="home-outer-div"> 
    
    
    
      <div id="login-box-background">
      </div>
        <div id="report-box">
            <div class="header-container">
                
                        
                        <div class="name">
                        <span id="profile-name"></span>
                         
                        <span id="position-dash"></span>
                        </div>

                        <div class="dash-logout">
                                <span id="dash-button"><i class="fas fa-tachometer-alt"></i></span>
                        </div>

               
            </div>
            <div class="left">
    <h1 class="dash-header" id="dash-header-title"></h1>

    <form id="report">
    <div class="select-container">
    <label for="pledgeSemester">Meeting</label>
        


      
    <select name='meetingName' id='meetingName' form='report' >
        
       
    </select>

    </div>

    <div class="select-container">

        <label for="reportStatus">Status</label>

        <select id="reportStatus" name="reportStatus" form="report">
            <option value="Report">Report</option>
            <option value="No Report" selected="selected">No Report</option>
        </select>
    </div>

    

    <div id="dynamic-input-container">
    </div>

    <div class="add-topic">
        <span class="add-topic-text">Add Topic</span>
        <i class="fas fa-plus"></i>
    </div>

    

    



    

    <input type="submit" name="report" value="Submit Report" id="btn-submit"/>




    </form>

    
  </div>
    
      <span id="result"></span>
      
      
      
      
    </div>
    
 
            
        </div>
    
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.serializeJSON/2.9.0/jquery.serializejson.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="util.js"></script>
    <script src="reports.js?v=1.1"></script>
    
    
    
    </body>
    
    
    </html>
    
    
    
    
    
    