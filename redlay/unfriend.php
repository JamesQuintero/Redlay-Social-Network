<?php
@include('init.php');
include('universal_functions.php');
$allowed="users";
include('security_checks.php');


$ID=(int)($_POST['user_id']);

if(is_id($ID) && user_id_exists($ID))
{
    if(user_is_friends($_SESSION['id'], $ID))
    {
        //deletes user from current user's friend list
        $query=mysql_query("SELECT user_friends, audience_groups, friend_timestamps FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
        if($query&&mysql_num_rows($query)==1)
        {
                $array=mysql_fetch_row($query);
                $friends=explode('|^|*|', $array[0]);
                $audiences=explode('|^|*|', $array[1]);
                $friend_timestamps=explode('|^|*|', $array[2]);

                $temp_friends=array();
                $temp_audiences=array();
                $temp_timestamps=array();
                for($x =0; $x < sizeof($friends); $x++)
                {
                    if($friends[$x]!=$ID)
                    {
                        $temp_friends[]=$friends[$x];
                        $temp_audiences[]=$audiences[$x];
                        $temp_timestamps[]=$friend_timestamps[$x];
                    }
                }
                $friends=implode('|^|*|', $temp_friends);
                $audiences=implode('|^|*|', $temp_audiences);
                $friend_timestamps=implode('|^|*|', $temp_timestamps);

                $query=mysql_query("UPDATE user_data SET user_friends='$friends', audience_groups='$audiences', friend_timestamps='$friend_timestamps' WHERE user_id=$_SESSION[id]");
                if($query)
                {
                    //deletes current user from user's friend list
                    $query=mysql_query("SELECT user_friends, audience_groups, friend_timestamps FROM user_data WHERE user_id=$ID LIMIT 1");
                    if($query&&mysql_num_rows($query)==1)
                    {
                        $array=mysql_fetch_row($query);
                        $friends=explode('|^|*|', $array[0]);
                        $audiences=explode('|^|*|', $array[1]);
                        $friend_timestamps=explode('|^|*|', $array[2]);


                        $temp_friends=array();
                        $temp_audiences=array();
                        $temp_timestamps=array();
                        for($x =0; $x < sizeof($friends); $x++)
                        {
                            if($friends[$x]!=$_SESSION['id'])
                            {
                                $temp_friends[]=$friends[$x];
                                $temp_audiences[]=$audiences[$x];
                                $temp_timestamps[]=$friend_timestamps[$x];
                            }
                        }
                        $friends=implode('|^|*|', $temp_friends);
                        $audiences=implode('|^|*|', $temp_audiences);
                        $friend_timestamps=implode('|^|*|', $temp_timestamps);

                        $query=mysql_query("UPDATE user_data SET user_friends='$friends', audience_groups='$audiences', friend_timestamps='$friend_timestamps' WHERE user_id=$ID");
                        if($query)
                        {
                            //remove user from current user's map if currently there
                            $query=mysql_query("SELECT added_items, added_item_types, added_position_grid FROM user_maps WHERE user_id=$_SESSION[id] LIMIT 1");
                            if($query&&mysql_num_rows($query)==1)
                            {
                                $array=mysql_fetch_row($query);
                                $added_items=explode('|^|*|', $array[0]);
                                $added_item_types=explode('|^|*|', $array[1]);
                                $added_position_grid=explode('|^|*|', $array[2]);

                                $temp_added_items=array();
                                $temp_added_item_types=array();
                                $temp_added_position_grid=array();

                                for($x =0; $x < sizeof($added_item); $x++)
                                {
                                    if($added_items[$x]!=$ID||$added_items_types[$x]!='user')
                                    {
                                        $temp_added_items[]=$added_items[$x];
                                        $temp_added_item_types[]=$added_item_types[$x];
                                        $temp_added_position_grid[]=$added_position_grid[$x];
                                    }
                                }

                                $added_items=implode('|^|*|', $temp_added_items);
                                $added_item_types=implode('|^|*|', $temp_added_item_types);
                                $added_position_grid=implode('|^|*|', $temp_added_position_grid);

                                $query=mysql_query("UPDATE user_maps SET added_items='$added_items', added_item_types='$added_item_types', added_position_grid='$added_position_grid' WHERE user_id=$_SESSION[id]");
                                if($query)
                                {
                                    //remove current user from user's map if currently there
                                    $query=mysql_query("SELECT added_items, added_item_types, added_position_grid FROM user_maps WHERE user_id=$ID LIMIT 1");
                                    if($query&&mysql_num_rows($query)==1)
                                    {
                                        $array=mysql_fetch_row($query);
                                        $added_items=explode('|^|*|', $array[0]);
                                        $added_item_types=explode('|^|*|', $array[1]);
                                        $added_position_grid=explode('|^|*|', $array[2]);

                                        $temp_added_items=array();
                                        $temp_added_item_types=array();
                                        $temp_added_position_grid=array();

                                        for($x =0; $x < sizeof($added_item); $x++)
                                        {
                                            if($added_items[$x]!=$_SESSION['id']||$added_items_types[$x]!='user')
                                            {
                                                $temp_added_items[]=$added_items[$x];
                                                $temp_added_item_types[]=$added_item_types[$x];
                                                $temp_added_position_grid[]=$added_position_grid[$x];
                                            }
                                        }

                                        $added_items=implode('|^|*|', $temp_added_items);
                                        $added_item_types=implode('|^|*|', $temp_added_item_types);
                                        $added_position_grid=implode('|^|*|', $temp_added_position_grid);

                                        $query=mysql_query("UPDATE user_maps SET added_items='$added_items', added_item_types='$added_item_types', added_position_grid='$added_position_grid' WHERE user_id=$ID");
                                        if(!$query)
                                        {
                                            echo "Something went wrong";
                                            log_error("unfriend.php: (11): ", mysql_error());
                                        }

                                    }
                                    else
                                    {
                                        echo "Something went wrong. We are working on fixing it";
                                        log_error("unfriend.php: (10): ", mysql_error());
                                    } 
                                }
                                else
                                {
                                    echo "Something went wrong. We are working on fixing it";
                                    log_error("unfriend.php: (9): ", mysql_error());
                                } 
                            }
                            else
                            {
                                echo "Something went wrong. We are working on fixing it";
                                log_error("unfriend.php: (6): ", mysql_error());
                            }
                                
                        }
                        else
                        {
                            echo "Something went wrong. We are working on fixing it";
                            log_error("unfriend.php: (3): ", mysql_error());
                        }
                    }
                    else
                    {
                        echo "Something went wrong. We are working on fixing it";
                        log_error("unfriend.php: (2): ", mysql_error());
                    }
                }
                else
                {
                    echo "Something went wrong. We are working on fixing it";
                    log_error("unfriend.php: (1): ", mysql_error());
                }
        }
        else
        {
            echo "Something went wrong. We are working on fixing it";
            log_error("unfriend.php: ", mysql_error());
        }
    }
    else
        echo "You cannot delete someone you have not added";
}
else
    echo "Invalid ID";