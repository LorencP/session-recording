<?php 
include_once("config.php");


class DataLayer{

    public function signUp(String $firstName, String $lastName, String $email, String $password){
        $token = bin2hex(random_bytes(16));
        $db = new db();
        $db = $db->connect();
        $sql = "Insert into User (FirstName, LastName, Email, Password, Token) Values(:firstName, :lastName, :email, :password, :token);";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt-> execute();

        $sql = "Select UserID, Token from User where Email = :email and Password = :password and :token = Token;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':token', $token);
        $stmt-> execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){
           
           setcookie("is-token", $row["Token"], time() + (86400 * 30), "/");
           setcookie("us-uid", $row["UserID"], time() + (86400 * 30), "/");

           return $row["UserID"];

            
           
        }

        return "";

    }

    public function saveNewBrother(int $userID, String $firstName, String $lastName, String $pledgeSemester, int $pledgeYear, String $status, String $oaklandEmail, String $phoneNumber, String $position){
        
        $db = new db();
        $db = $db->connect();
        $sql = "Insert into Brother (UserID, FirstName, LastName, PledgeSemester, PledgeYear, OaklandEmail, PhoneNumber, Status, Position, CreateDate, LastUpdatedDate) Values(:userID, :firstName, :lastName, :pledgeSemester, :pledgeYear, :oaklandEmail, :phoneNumber, :status, :position, Now(), Now());";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':pledgeSemester', $pledgeSemester);
        $stmt->bindParam(':pledgeYear', $pledgeYear);
        $stmt->bindParam(':oaklandEmail', $oaklandEmail);
        $stmt->bindParam(':phoneNumber', $phoneNumber);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':position', $position);
        $stmt-> execute();

        print_r($stmt->errorInfo()); 
     

       

        $sql = "Select BrotherID, Position, FirstName, LastName, IsPrudential from Brother where UserID = :userID;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt-> execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){

           setcookie("asp-first-name", $row["FirstName"], time() + (86400 * 30), "/");
           setcookie("asp-last-name", $row["LastName"], time() + (86400 * 30), "/");
           setcookie("asp-position", $row["Position"], time() + (86400 * 30), "/");
           setcookie("asp-brother-id", $row["BrotherID"], time() + (86400 * 30), "/");
           setcookie("asp-pr", $row["IsPrudential"], time() + (86400 * 30), "/");
           
           return $row["BrotherID"];
        }

        return "";



    }

    public function doesEmailExist(String $email){
        $db = new db();
        $db = $db->connect();
        $sql = "Select * From User where OaklandEmail =  :email;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt-> execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($rows != null){
            return true;
        }else{
            return false;
        }

    }


    public function signIn(String $password, String $oaklandEmail){
        echo("entered sign in methods. The password is " . $password);
        $token = bin2hex(random_bytes(16));
        $db = new db();
        $db = $db->connect();
        $sql = "Select b.UserID, u.Password, b.FirstName, b.LastName, b.Position, b.BrotherID, b.IsPrudential From User u join Brother b on b.UserID = u.UserID where u.OaklandEmail = :oaklandEmail and u.OUASPELKEY = 'ouaspel';";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':oaklandEmail', $oaklandEmail);
        $stmt-> execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){
           $userId = $row["UserID"];
           $firstName = $row["FirstName"];
           $lastName = $row["LastName"];
           $passwordHashFromDb = $row["Password"];
           $position = $row["Position"];
           $brotherID = $row["BrotherID"];
           $isPrudential = $row["IsPrudential"];
           $validPassword = password_verify($password, $passwordHashFromDb);
           if($validPassword == true){
            $db = new db();
            $db = $db->connect();
            $sql = "Update User Set Token = :token, TokenCreateDate = NOW() where UserID = :userID";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userID', $userId);
            $stmt->bindParam(':token', $token);
            $stmt-> execute();

            setcookie("asp-token", $token, time() + (86400 * 30), "/");
            setcookie("asp-first-name", $firstName, time() + (86400 * 30), "/");
            setcookie("asp-last-name", $lastName, time() + (86400 * 30), "/");
            setcookie("asp-user-id", $userId, time() + (86400 * 30), "/");
            setcookie("asp-position", $position, time() + (86400 * 30), "/");
            setcookie("asp-brother-id", $brotherID, time() + (86400 * 30), "/");
            setcookie("asp-pr", $isPrudential, time() + (86400 * 30), "/");



           
            return true;
               
           }else{//the password did not match the email
               return false;
           }
        }

        
       
    }


    function tokenAuth($token, $userID){

        $newToken = bin2hex(random_bytes(16));
        $db = new db();
        $db = $db->connect();
        $sql = "Select b.UserID, b.FirstName, b.LastName, b.Position, b.BrotherID, b.IsPrudential From User u join Brother b on b.UserID = u.UserID where u.token = :token";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt-> execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){
           $userId = $row["UserID"];
           $firstName = $row["FirstName"];
           $lastName = $row["LastName"];
           $position = $row["Position"];
           $brotherID = $row["BrotherID"];
           $isPrudential = $row["IsPrudential"];
           
            $db = new db();
            $db = $db->connect();
            $sql = "Update User Set Token = :token, TokenCreateDate = NOW() where UserID = :userID";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userID', $userId);
            $stmt->bindParam(':token', $newToken);
            $stmt-> execute();

            setcookie("asp-token", $newToken, time() + (86400 * 30), "/");
            setcookie("asp-first-name", $firstName, time() + (86400 * 30), "/");
            setcookie("asp-last-name", $lastName, time() + (86400 * 30), "/");
            setcookie("asp-user-id", $userId, time() + (86400 * 30), "/");
            setcookie("asp-position", $position, time() + (86400 * 30), "/");
            setcookie("asp-brother-id", $brotherID, time() + (86400 * 30), "/");
            setcookie("asp-pr", $isPrudential, time() + (86400 * 30), "/");


           return true;
            
        }

        return false;


    }

    function getUpcomingMeetings(){

        $userId = null;
        $token = bin2hex(random_bytes(16));
        $db = new db();
        $db = $db->connect();
        $sql = "Select e.Name, e.EventID from Event e join Meeting m on m.EventID = e.EventID order by e.Date asc";
        $stmt = $db->prepare($sql);
        $stmt-> execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $select = "";
        foreach($rows as $row){
            unset($meetingName);
            $meetingName = $row["Name"];
            $eventId = $row["EventID"];

            $select .= '<option value="'.$eventId.'">'.$meetingName.'</option>';
        }

        return $select;


    }

    function getAllMeetings(){
        
        $db = new db();
        $db = $db->connect();
        $sql = "Select e.Name, e.EventID from Event e join Meeting m on m.EventID = e.EventID order by e.Date asc";
        $stmt = $db->prepare($sql);
        $stmt-> execute();

        $allMeetings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $allMeetings;
   
    }

    function getNextMeeting(){

        $db = new db();
        $db = $db->connect();
        $sql = "Select e.EventID from Event e join Meeting m on m.EventID = e.EventID where e.Date >  DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $db->prepare($sql);
        $stmt-> execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

       
        foreach($rows as $row){
            
            $eventID = $row["EventID"];

            return $eventID;
        }

        return 1;

    }

    function saveReport($brotherID, $position, $status, $meetingID){

        echo("MeetingID is " . $meetingID);
        echo("BrotherID is " . $brotherID);
        $testIfReportExists = null;
        //first, let's make sure that the report is not already submitted
        $db = new db();
        $db = $db->connect();
        $sql = "Select ReportID from Report where BrotherID = :brotherID and MeetingID = :meetingID and Position = :position;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':brotherID', $brotherID);
        $stmt->bindParam(':meetingID', $meetingID);
        $stmt->bindParam(':position', $position);
        $stmt-> execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
            if($rows == null || $rows == ""){

                echo("rows is null. ");
           
                $db = new db();
                $db = $db->connect();
                $sql = "Insert into Report (BrotherID, Position, Status, MeetingID, CreateDate, LastUpdatedDate) Values(:brotherID, :position, :status, :meetingID, NOW(), NOW());";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':brotherID', $brotherID);
                $stmt->bindParam(':position', $position);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':meetingID', $meetingID);
                $stmt-> execute();
                
            }else{//report already exists for this brother and this meeting

                $db = new db();
                $db = $db->connect();
                $sql = "Update Report Set Status = :status, LastUpdatedDate = NOW() where BrotherID = :brotherID and Position = :position and MeetingID = :meetingID";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':brotherID', $brotherID);
                $stmt->bindParam(':position', $position);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':meetingID', $meetingID);
                $stmt-> execute();

            }



            $sql = "Select ReportID from Report where BrotherID = :brotherID and MeetingID = :meetingID; and Position = :position;";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':brotherID', $brotherID);
            $stmt->bindParam(':meetingID', $meetingID);
            $stmt->bindParam(':position', $position);
            $stmt-> execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($rows as $row){
                return $row["ReportID"];
            }

        return "";
    }

    function saveReportTopic($reportID, $topic, $body){

        $db = new db();
        $db = $db->connect();
        $sql = "Insert into ReportTopic (ReportID, Topic, Body) Values(:reportID, :topic, :body);";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':reportID', $reportID);
        $stmt->bindParam(':topic', $topic);
        $stmt->bindParam(':body', $body);
        $stmt-> execute();


        //validate that the reportTopicRecord was added
        $sql = "Select ReportTopicID From ReportTopic where ReportID = :reportID and Topic = :topic and Body = :body;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':reportID', $reportID);
        $stmt->bindParam(':topic', $topic);
        $stmt->bindParam(':body', $body);
        $stmt-> execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){
            return $row["ReportTopicID"];
         }




    }

    

    function getMeetingID($eventID){
        $db = new db();
        $db = $db->connect();
        $sql = "Select MeetingID from Meeting where EventID = :eventID;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':eventID', $eventID);
        $stmt-> execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){
           return $row["MeetingID"];
        }

        return "";
    }
    
    function getReportByEventIDBrotherID($eventID, $brotherID){

        $db = new db();
        $db = $db->connect();
        $sql = "Select b.FirstName, b.LastName, r.Position, rt.Topic, rt.Body 
                  from Report r 
                  join Meeting m on m.MeetingID = r.MeetingID
                  join ReportTopic rt on rt.ReportID = r.ReportID
                  join Brother b on b.BrotherID = r.BrotherID
                 where m.EventID = :eventID 
                   and r.BrotherID = :brotherID;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->bindParam(':brotherID', $brotherID);
        
        $stmt-> execute();
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $reportData;

    }

    function  getReportByEventID($eventID){

        $db = new db();
        $db = $db->connect();
        $sql = "Select b.FirstName, b.LastName, b.Position, rt.Topic, rt.Body
                  from Report r 
                  join Meeting m on m.MeetingID = r.MeetingID
                  join ReportTopic rt on rt.ReportID = r.ReportID
                  join Brother b on b.BrotherID = r.BrotherID
                 where m.EventID = :eventID
                 order by b.DisplayOrder asc;";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':eventID', $eventID);
       
        $stmt-> execute();
        $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $reportData;

    }

   

    function getReportByCookieCriteria($eventID){

        $db = new db();
        $db = $db->connect();
        $sql = "Select b.FirstName, b.LastName, r.Position, rt.Topic, rt.Body 
                  from Report r 
                  join Meeting m on m.MeetingID = r.MeetingID
                  join ReportTopic rt on rt.ReportID = r.ReportID
                  join Brother b on b.BrotherID = r.BrotherID
                 where m.EventID = :eventID 
                   and r.BrotherID = :brotherID 
                   and r.Position = :position
                   and r.Status = 'Report';";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->bindParam(':brotherID', $_COOKIE['asp-brother-id']);
        $stmt->bindParam(':position', $_COOKIE['asp-position']);
       

        $stmt-> execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $htmlBuilder = "";

        foreach($rows as $row){
           $topic = $row["Topic"];
           $body = $row["Body"];

           $htmlBuilder .= "<h3 class='report-topic-heading'>" . $topic . "</h3>" . "<div class='report-body'>" . $body . "</div>";
        }

        return $htmlBuilder;

    }

   function getAllReportsByCookieEventID(){
    $eventID = 3;
    $db01 = new db();
    $db01 = $db01->connect();
    $sql01 = "Select r.ReportID, b.FirstName, b.LastName, b.Position from Brother b join Report r on r.BrotherID = b.BrotherID join Meeting m on m.MeetingID = r.MeetingID where m.eventID = :eventID;";
    $stmt01 = $db01->prepare($sql01);
    $stmt01->bindParam(':eventID', $eventID);
    $stmt01-> execute();
    $rows01 = $stmt01->fetchAll(PDO::FETCH_ASSOC);

    $htmlBuilder = "";

        foreach($rows01 as $row01){

            $position = $row01["Position"];
            $firstName = $row01["FirstName"];
            $lastName = $row01["LastName"];

            $htmlBuilder .= "<h2 class='report-position-heading'>" . $position . " | " . $firstName . " " . $lastName . "</h2><div class='report-bar'></div><br />";

            $reportID = $row01["ReportID"];
           

          
           $db = new db();
           $db = $db->connect();
           $sql = "Select rt.Topic, rt.Body 
                    from Report r 
                    join Meeting m on m.MeetingID = r.MeetingID
                    left join ReportTopic rt on rt.ReportID = r.ReportID
                    join Brother b on b.BrotherID = r.BrotherID
                   where r.ReportID = :reportID";
                          
               $stmt = $db->prepare($sql);
               $stmt->bindParam(':reportID', $reportID);
       
               $stmt-> execute();
               $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
               
               foreach($rows as $row){
                  $topic = $row["Topic"];
                  $body = $row["Body"];
       
                  $htmlBuilder .= "<h3 class='all-report-topic-heading'>" . $topic . "</h3>" . "<div class='all-report-body'>" . $body . "</div>";
               }
        }

   

        return $htmlBuilder;


   }

   function getUpcomingEvents(){

    $userId = null;
    $db = new db();
    $db = $db->connect();
    $sql = "Select e.Name, e.EventID from Event e order by e.Date asc";
    $stmt = $db->prepare($sql);
    $stmt-> execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $select = "";
    foreach($rows as $row){
        unset($eventName);
        $eventName = $row["Name"];
        $eventId = $row["EventID"];

        $select .= '<option value="'.$eventId.'">'.$eventName.'</option>';
    }

    return $select;


}

function saveExcuseAbsence($brotherID, $eventID, $excuseReason){

    $db = new db();
    $db = $db->connect();
    $sql = "Insert into EventExcused (EventID, BrotherID, Reason, Approved, CreateDate, LastUpdateDate) Values(:eventID, :brotherID, :reason, 0, Now(), Now());";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':brotherID', $brotherID);
    $stmt->bindParam(':eventID', $eventID);
    $stmt->bindParam(':reason', $excuseReason);
    $stmt-> execute();  

    $sql = "Select EventExcusedID from EventExcused where EventID = :eventID and BrotherID = :brotherID and Reason = :reason;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':brotherID', $brotherID);
    $stmt->bindParam(':eventID', $eventID);
    $stmt->bindParam(':reason', $excuseReason);
    $stmt-> execute();  

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

   
    foreach($rows as $row){
       
       return $row["EventExcusedID"];

    }

    return;

}

function getExcusedAbsencesByEventID($eventID){

    $db = new db();
    $db = $db->connect();
    $sql = "Select b.BrotherID, b.FirstName, b.LastName, ee.Reason, ee.CreateDate, ee.Approved from EventExcused ee join Brother b on b.BrotherID = ee.BrotherID where ee.EventID = :eventID order by ee.CreateDate desc;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':eventID', $eventID);
    $stmt-> execute();  

    $excuses = $stmt->fetchAll(PDO::FETCH_ASSOC);



    return $excuses;

}


function getActiveBrotherList(){

    $db = new db();
    $db = $db->connect();
    $sql = "Select * from Brother;";
    $stmt = $db->prepare($sql);
    $stmt-> execute();  

    $brothers = $stmt->fetchAll(PDO::FETCH_ASSOC);



    return $brothers;

}

function saveAttendanceByBrotherIDEventID($brotherID, $eventID){

    $db = new db();
    $db = $db->connect();
    $sql = "INSERT INTO Attendance (EventID, BrotherID, Present, EventExcusedID, CreateDate,  LastUpdateDate) VALUES (:eventID, :brotherID, 1, 'NULL', NOW(), NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':eventID', $eventID);
    $stmt->bindParam(':brotherID', $brotherID);
    $stmt-> execute();  

    
    $sql = "Select AttendanceID, b.FirstName, b.LastName from Attendance a join Brother b on b.BrotherID = a.BrotherID where a.BrotherID = :brotherID and EventID = :eventID;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':eventID', $eventID);
    $stmt->bindParam(':brotherID', $brotherID);
    $stmt-> execute();  

    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($attendance as $row){
       
        return $row["AttendanceID"];
 
     }



    return;




}

function getAttendanceByEventID($eventID){

    $db = new db();
    $db = $db->connect();
    $sql = "Select b.FirstName, b.LastName, b.BrotherID, a.Present from Brother b join Attendance a on a.BrotherID = b.BrotherID where a.EventID = :eventID;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':eventID', $eventID);
    $stmt-> execute();  

    $brothers = $stmt->fetchAll(PDO::FETCH_ASSOC);



    return $brothers;

}

function getWordOfDay($eventID){

    $db = new db();
    $db = $db->connect();
    $sql = "Select BekimWordOfDay from Meeting where EventID = :eventID;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':eventID', $eventID);
    $stmt-> execute();  

    $wordOfDay = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($wordOfDay as $row){
       
        return $row["BekimWordOfDay"];
 
    }

    return;

}

function getApprovedExcusedAbsencesByBrotherIDEventID($brotherID, $eventID){

    $db = new db();
    $db = $db->connect();
    $sql = "Select b.BrotherID, b.FirstName, b.LastName, ee.Reason, ee.CreateDate from EventExcused ee join Brother b on b.BrotherID = ee.BrotherID where ee.EventID = :eventID and ee.BrotherID = :brotherID and ee.Approved = 1 order by ee.CreateDate desc;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':eventID', $eventID);
    $stmt->bindParam(':brotherID', $brotherID);
    $stmt-> execute();  

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $valid = false;

    foreach($rows as $row){
       
        $valid = true;
 
    }

    return $valid;

}

function getPaperMeeting($eventID){

    $db = new db();
    $db = $db->connect();
    $sql = "Select BekimWordOfDay from Meeting where EventID = :eventID and IsPaperMeeting = true;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':eventID', $eventID);
    $stmt-> execute(); 
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $isPaperMeeting = false;

    foreach($rows as $row){
       
        $isPaperMeeting = true;
 
    }

    return $isPaperMeeting;

}

function getAttendanceTotalsByBrotherID($brotherID){

    $db = new db();
    $db = $db->connect();
    $sql = "Select b.FirstName, b.LastName, e.EventTypeID, count(*) as Count From Brother b 
              join Attendance a on a.BrotherID = b.BrotherID 
              join Event e on e.EventID = a.EventID
             where a.BrotherID = :brotherID and e.EventTypeID = 1
          group by b.FirstName, b.LastName, e.EventTypeID";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':brotherID', $brotherID);
    $stmt-> execute(); 
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($rows as $row){
       
        return $row["Count"];
 
    }
    
}
}

?>