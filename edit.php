<?php
    require_once 'inc/admin.php';

    $eventId='';
    $eventName='';
    $eventCategory=(!empty($_REQUEST['category'])?intval($_REQUEST['category']):'');
    $eventDate='';
    $eventDescription='';

    if(!empty($_REQUEST['id'])){
        $eventQuery=$db->prepare('SELECT * FROM events WHERE event_id=:id LIMIT 1;');
        $eventQuery->execute([':id'=>$_REQUEST['id']]);
        if($event=$eventQuery->fetch(PDO::FETCH_ASSOC)){
            $eventId=$event['event_id'];
            $eventName=$event['name'];
            $eventCategory=$event['category_id'];
            $eventDate=$event['date'];
            $eventDescription=$event['description'];
        }else{
            exit('This event doesn\'t exist.');
        }
    }

    $errors = [];
    if(!empty($_POST)){
        $eventName=trim(@$_POST['name']);
        if(empty($eventName)){
            $errors['name']='Please, provide a name of the event.';
        }
        if(!empty($_POST['category'])){
            $categoryQuery=$db->prepare('SELECT * FROM category WHERE category_id=:category LIMIT 1;');
            $categoryQuery->execute([
                    ':category'=>$_POST['category']
            ]);
            if($categoryQuery->rowCount()==0){
                $errors['category']='Chosen category doesn\'t exist';
                $eventCategory='';
            }else{
                $eventCategory=$_POST['category'];
            }
        }else{
            $errors['category']='Please, choose a category.';
        }
        $todayDate=date('d-m-Y');
        $eventDate=$_POST['date'];
        $eventsDate=date('d-m-Y', strtotime($_POST['date']));
        if($todayDate >= $eventsDate){
            $errors['date']='Please, provide a date greater that today.';
        }
        $eventDescription=trim(@$_POST['description']);
        if(empty($eventDescription)){
            $errors['description']='Please, fill in the description section';
        }
        if(empty($errors)) {
            if ($eventId) {
                $saveQuery = $db->prepare('UPDATE events SET name=:name, category_id=:category, date=:date, description=:description WHERE event_id=:id LIMIT 1');
                $saveQuery->execute([
                    ':id' => $eventId,
                    ':name' => $eventName,
                    ':category' => $eventCategory,
                    ':date' => $eventDate,
                    ':description' => $eventDescription
                ]);
            } else {
                $saveQuery = $db->prepare('INSERT INTO events (name,date,description,category_id) VALUES (:name,:date,:description,:category);');
                $saveQuery->execute([
                    ':name' => $eventName,
                    ':date' => $eventDate,
                    ':description' => $eventDescription,
                    ':category' => $eventCategory
                ]);
            }
            header('Location: index.php?category=' . $eventCategory);
            exit();
        }
    }
    include 'inc/header.php';
?>

    <form method="post">
        <input type="hidden" name="id" value="<?php echo $eventId;?>" />
        <div class="form-group">
            <label for="name">Name of the event:</label>
            <textarea name="name" id="name" required class="form-control<?php echo (!empty($errors['name'])?' is-invalid':''); ?>"><?php echo htmlspecialchars($eventName)?></textarea>
            <?php
                if(!empty($errors['name'])){
                    echo '<div class="invalid-feedback">'.$errors['name'].'</div>';
                }
            ?>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <select name="category" id="category" required class="form-control <?php echo (!empty($errors['category'])?' is-invalid':''); ?>">
                <option value="">Choose a category</option>
                <?php
                    $categoryQuery=$db->prepare('SELECT * FROM category ORDER BY name;');
                    $categoryQuery->execute();
                    $categories=$categoryQuery->fetchAll(PDO::FETCH_ASSOC);
                    if(!empty($categories)){
                        foreach ($categories as $category){
                            echo '<option value="'.$category['category_id'].'"'.($category['category_id']==$eventCategory?'selected="selected"':'').'>'.htmlspecialchars($category['name']).'</option>';
                        }
                    }
                ?>
            </select>
            <?php
                if(!empty($errors['category'])){
                    echo '<div class="invalid-feedback">'.$errors['category'].'</div>';
                }
            ?>
        </div>
        <div class="form-group">
            <label for="date">Event date:</label>
            <input type="date" id="date" name="date" required class="form-control<?php echo (!empty($errors['date'])?' is-invalid':''); ?>">
            <?php
                if(!empty($errors['date'])){
                    echo '<div class="invalid-feedback">'.$errors['date'].'</div>';
                }
            ?>
        </div>
        <div class="form-group">
            <label for="description">Description of the event:</label>
            <textarea name="description" id="description" required class="form-control<?php echo (!empty($errors['description'])?' is-invalid':'');?>"><?php echo htmlspecialchars($eventDescription)?></textarea>
            <?php
                if(!empty($errors['description'])){
                    echo '<div class="invalid-feedback">'.$errors['description'].'</div>';
                }
            ?>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Post</button>
        <a href="index.php?category=<?php echo $eventCategory;?>" class="btn btn-secondary">Cancel</a>
    </form>

<?php
    include 'inc/footer.php';