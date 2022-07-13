<?php
    require_once 'inc/user.php';
    include 'inc/header.php';

    #region offset pagination
    if(isset($_GET['offset'])){
    $offset=(int)$_GET['offset'];
    }else{
        $offset=0;
    }
    #endregion offset pagination
    $count=$db->query('SELECT COUNT(event_id) from events')->fetchColumn();

    if(!empty($_GET['category'])) {
        #region Choose event by category
        $query = $db->prepare("SELECT events.*, category.name AS category_name FROM events JOIN category USING (category_id) WHERE events.category_id=? ORDER BY date LIMIT 10 OFFSET ?; ");
        //
        //
        $query->bindValue(1,$_GET['category'],PDO::PARAM_STR);
        $query->bindValue(2,$offset,PDO::PARAM_INT);
        $query->execute();
        #endregion Choose event by category
    }else{
        #region Choose all events
        $query = $db->prepare('SELECT events.*, category.name AS category_name FROM events JOIN category USING (category_id) ORDER BY date LIMIT 10 OFFSET ?; ');
        $query->bindValue(1,$offset,PDO::PARAM_INT);
        $query->execute();
        #endregion Choose all events
    }

    #region Choose category form

    echo '<form method="get" id="categoryFilterForm">
            <label for="category">Category:</label>
            <select name="category" id="category" onchange="document.getElementById(\'categoryFilterForm\').submit();">
                <option value="">All</option>';
    $categories=$db->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($categories)){
        foreach ($categories as $category){
            echo '<option value="'.$category['category_id'].'"';
            if ($category['category_id'] == @$_GET['category']){
                echo ' selected="selected" ';
            }
            echo '>'.htmlspecialchars($category['name']).'</option>';
        }
    }
    echo '      </select>
            </form>';
    #endregion Choose category form

    $events = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($events)){
        echo '<div class="row justify-content-center d-flex">';
        foreach ($events as $event){
            echo '<div class="col-md-5 flex-grow-1 my-1 mx-1 px-md-2 py-md-2 border border-dark rounded">';
            echo '<div class="lead">';
                echo htmlspecialchars($event['name']);
                if (@$currentUser['role'] == 'admin') {
                    echo '  - <a href="edit.php?id=' . $event['event_id'] . '"class="btn btn-warning btn-sm">Edit</a>';
                    echo ' <a href="delete.php?id='.$event['event_id'].'"class="btn btn-danger btn-sm">Delete</a>';
                }
                echo '<br>';
                echo date('d.m.Y', strtotime($event['date']));
            echo '</div>';
            echo '<div><span class="badge bg-secondary my-2">'.htmlspecialchars($event['category_name']).'</span></div>';
            echo '<div class="h-auto border rounded  px-md-2 overflow-auto">'.nl2br(htmlspecialchars($event['description'])).'</div>';
            #region check for attendance
            $attendanceQuery = $db->prepare('SELECT COUNT(user_id) as total FROM attend WHERE event_id=:event_id');
            $attendanceQuery->execute([
                ':event_id'=>$event['event_id']
            ]);
            $attendance=$attendanceQuery->fetch(PDO::FETCH_ASSOC);
            #endregion check for attendance
            #region check if already registered
            $checkRegistration = $db->prepare('SELECT * FROM attend WHERE user_id=? AND event_id=?');
            $checkRegistration->execute([
               @$currentUser['user_id'],
               $event['event_id']
            ]);
            #endregion check if already registered
            @$attend = ["user_id" => $currentUser['user_id'],"event_id" => $event['event_id']];
            echo '<div class="alert alert-info my-2"> There are '. $attendance['total'] .' people registered for this event.</div>';
            if (isset($_SESSION['user_id'])){
                if ($attend!=$checkRegistration->fetch(PDO::FETCH_ASSOC)){
                    echo '<a href="attend.php?id='.$event['event_id'].'"class="btn btn-success btn-sm">Register for this event</a>';
                } else {
                    echo '<div class="badge bg-secondary text-wrap">You\'re registered for this event.</div>';
                }
            }else{
                echo '<a href="login.php" class="badge bg-primary text-wrap">Please login to register for this event!</a>';
            }
            echo '</div>';
        }
        echo '</div>';
    }else{
        echo '<div class="alert"> No events were found </div>';
    }
    echo '<nav aria-label="Page navigation">';
    echo '<ul class="pagination">';
          for($i=1; $i<=ceil($count/10); $i++){
              echo '<li class="page-item">';
              echo '<a class="page-link'.($offset/10+1==$i?'active':'').'" href="index.php?offset='.(($i-1)*10).'">'.$i.'</a>';
              echo '</li>';
          }
    echo '</ul>';
    echo '</nav>';

    if (@$currentUser['role']=='admin') {
        echo '<div class="row my-3">
            <a href="edit.php?category=' . @$_GET['category'] . '" class="btn btn-primary">Add an event</a>
          </div>';
    }

    include 'inc/footer.php';
