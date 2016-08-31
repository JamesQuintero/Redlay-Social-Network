<?php
@include('init.php');
include('universal_functions.php');
$allowed="both";
include('security_checks.php');


$num=(int)($_POST['num']);
$parameters=$_POST['parameters'];


$page=(int)($_POST['page']);

if(is_array($parameters)&&$page>=0);
{
    //checks arrays
    if(($num==1&&($parameters[0]!=''||$parameters[1]!=''||$parameters[2]!=''||$parameters[3]!=''||$parameters[4]!=''))||($num==2&&($parameters[0]!=''||$parameters[1]!=''))||($num==3&&($parameters[0]!=''||$parameters[1]!=''))||($num==4&&$parameters[0]!=''))
    {
        //searches for users
        if($num==1)
        {
            $ids=array();
            $closed=array();
            $names=array();
            $is_friends=array();
            $has_liked=array();
            $pending_friends=array();
            $add_request_sent=array();
            $user_descriptions=array();
            $num_friends=array();
            $num_likes=array();
            $page_descriptions=array();
            $profile_pictures=array();
            $badges=array();

            //if first name exists
            if($parameters[0]!=''&&$parameters[1]=='')
                $query=mysql_query("SELECT id FROM users WHERE firstName='$parameters[0]'");

            //if last name exists
            else if($parameters[0]==''&&$parameters[1]!='')
                $query=mysql_query("SELECT id FROM users WHERE lastName='$parameters[1]'");

            //if first and last name exist
            else if($parameters[0]!=''&&$parameters[1]!='')
                $query=mysql_query("SELECT id FROM users WHERE firstName='$parameters[0]' AND lastName='$parameters[1]'");


            //gets the names from name parameters if set
            if($parameters[0]!=''||$parameters[1]!='')
            {
                if($query&&mysql_num_rows($query)!=0)
                {
                    for($x = 0; $x < mysql_num_rows($query); $x++)
                    {
                        //gets every user with the matching names
                        $array[$x]=mysql_fetch_row($query);
                        $ids[]=$array[$x][0];
                        
                        //gets user's closed status
                        $query2=mysql_query("SELECT closed FROM closed_accounts WHERE user_id=".$array[$x][0]." LIMIT 1");
                        if(mysql_num_rows($query2)==1)
                        {
                            $array2=mysql_fetch_row($query2);
                            $closed[]=$array2[0];
                        }
                        else
                            $closed[]='no';
                        
                    }

                    //gets search privacy and takes out ids where they don't want to be searched
                    //also gets the user's names
                    $temp_ids=array();
                    for($y = 0; $y < sizeof($ids); $y++)
                    {
                        $query=mysql_query("SELECT search_options FROM user_privacy WHERE user_id=".$ids[$y]);
                        if($query&&mysql_num_rows($query)==1)
                        {
                            $array=mysql_fetch_row($query);
                            $search_options=explode('|^|*|', $array[0]);
                            
                            //if user allows themselves to be searched and their account isn't closed
                            if($search_options[0]=='yes'&&$closed[$y]=='no')
                            {
                                $temp_ids[]=$ids[$y];
                                $temp_names[]=get_user_name($ids[$y]);

                                //checks to see if friends or pending friend request
                                $query=mysql_query("SELECT user_friends, user_bio FROM user_data WHERE user_id=$ids[$y] LIMIT 1");
                                if($query&&mysql_num_rows($query)==1)
                                {
                                    $array=mysql_fetch_row($query);
                                    $friends=explode('|^|*|', $array[0]);

                                    if($array[0]!='')
                                        $num_friends[]=sizeof($friends);
                                    else
                                        $num_friends[]=0;
                                    $user_descriptions[]=$array[1];
                                    
                                    //gets profile pictures
                                    $profile_pictures[]=get_profile_picture($ids[$y]);
                                    
                                    $badges[]=get_badges($ids[$y]);
                                    
                                    if(in_array($_SESSION['id'], $friends))
                                    {
                                        $is_friends[]='true';
                                        $pending_friends[]='false';
                                        $add_request_sent[]=0;
                                    }
                                    else
                                    {
                                        $is_friends[]='false';
                                        
                                        //checks whether there is a pending friend request
                                        $query=mysql_query("SELECT other_user_id, user_sent FROM pending_friend_requests WHERE user_id=$ids[$y]");
                                        if($query&&mysql_num_rows($query)==1)
                                        {
                                            $array=mysql_fetch_row($query);
                                            $other_user_ids=explode('|^|*|', $array[0]);
                                            $users_sent=explode('|^|*|', $array[1]);

                                            $pending=false;
                                            for($z = 0; $z < sizeof($other_user_ids); $z++)
                                            {
                                                if($other_user_ids[$z]==$_SESSION['id'])
                                                {
                                                    $pending=true;

//                                                    if($users_sent[$z]==$_SESSION['id'])
//                                                        $add_request_sent[]=true;
//                                                    else
//                                                        $add_request_sent[]=false;
                                                    $add_request_sent[]=$users_sent[$z];
                                                }
                                            }

                                            if($pending)
                                                $pending_friends[]='true';
                                            else
                                                $pending_friends[]='false';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $ids=$temp_ids;
                    $names=$temp_names;
                }
                else
                {

                    $JSON=array();
                    $JSON['ids']=array();
                    $JSON['names']=array();
                    $JSON['is_friends']=array();
                    $JSON['has_liked']=array();
                    $JSON['pending_friends']=array();
                    $JSON['add_request_sent']=array();
                    $JSON['user_descriptions']=array();
                    $JSON['num_adds']=array();
                    $JSON['num_likes']=array();
                    $JSON['page_descriptions']=array();
                    $JSON['profile_pictures']=array();
                    $JSON['badges']=array();
                    echo json_encode($JSON);
                    exit();
                }
            }


            //gets rest of parameters if set
            if($parameters[2]!=''||$parameters[3]!=''||$parameters[4]!='')
            {
                if(!empty($ids))
                {
                    //gets these parameters from already set array of user_ids
                    $temp_ids=array();
                    $temp_names=array();

                    for($x = 0; $x < sizeof($ids); $x++)
                    {
                        //gets parameters depending on which ones the user selected
                        if($parameters[2]!=''&&$parameters[3]==''&&$parameters[4]=='')
                            $query=mysql_query("SELECT user_friends, user_bio FROM user_data WHERE user_id=$ids[$x] AND high_school='$parameters[2]' LIMIT 1");
                        else if($parameters[2]==''&&$parameters[3]!=''&&$parameters[4]=='')
                            $query=mysql_query("SELECT user_friends, user_bio FROM user_data WHERE user_id=$ids[$x] AND college='$parameters[3]' LIMIT 1");
                        else if($parameters[2]==''&&$parameters[3]==''&&$parameters[4]!='')
                            $query=mysql_query("SELECT user_friends, user_bio FROM user_data WHERE user_id=$ids[$x] AND city='$parameters[4]' LIMIT 1");
                        else if($parameters[2]!=''&&$parameters[3]!=''&&$parameters[4]=='')
                            $query=mysql_query("SELECT user_friends, user_bio FROM user_data WHERE user_id=$ids[$x] AND high_school='$parameters[2]' AND college='$parameters[3]' LIMIT 1");
                        else if($parameters[2]!=''&&$parameters[3]==''&&$parameters[4]!='')
                            $query=mysql_query("SELECT user_friends, user_bio FROM user_data WHERE user_id=$ids[$x] AND high_school='$parameters[2]' AND city='$parameters[4]' LIMIT 1");
                        else if($parameters[2]==''&&$parameters[3]!=''&&$parameters[4]!='')
                            $query=mysql_query("SELECT user_friends, user_bio FROM user_data WHERE user_id=$ids[$x] AND college='$parameters[3]' AND city='$parameters[4]' LIMIT 1");
                        else if($parameters[2]!=''&&$parameters[3]!=''&&$parameters[4]!='')
                            $query=mysql_query("SELECT user_friends, user_bio FROM user_data WHERE user_id=$ids[$x] AND high_school='$parameters[2]' AND college='$parameters[3]' AND city='$parameters[4]' LIMIT 1");
                        
                        if($query&&mysql_num_rows($query)==1)
                        {
                            $temp_ids[]=$ids[$x];
                            $temp_names[]=get_user_name($ids[$x]);

                            
                            $array=mysql_fetch_row($query);
                            $friends=explode('|^|*|', $array[0]);

                            if($array[0]!='')
                                $num_friends[]=sizeof($friends);
                            else
                                $num_friends[]=0;
                            $user_descriptions[]=$array[1];
                            
                            //gets profile pictures
                            $profile_pictures[]=get_profile_picture($ids[$x]);
                            
                            
                            if(in_array($_SESSION['id'], $friends))
                            {
                                $is_friends[]='true';
                                $pending_friends[]='false';
                                $add_request_sent[]=0;
                            }
                            else
                            {
                                $is_friends[]='false';
                                
                                //checks whether there is a pending friend request
                                $query=mysql_query("SELECT other_user_id, user_sent FROM pending_friend_requests WHERE user_id=$ids[$x]");
                                if($query&&mysql_num_rows($query)==1)
                                {
                                    $array=mysql_fetch_row($query);
                                    $other_user_ids=explode('|^|*|', $array[0]);
                                    $users_sent=explode('|^|*|', $array[1]);
                                    
                                    $pending=false;
                                    for($y = 0; $y < sizeof($other_user_ids); $y++)
                                    {
                                        if($other_user_ids[$y]==$_SESSION['id'])
                                        {
                                            $pending=true;
                                            $add_request_sent[]=$users_sent[$y];
                                        }
                                    }
                                    
                                    if($pending)
                                        $pending_friends[]='true';
                                    else
                                        $pending_friends[]='false';
                                }
                            }
                        }
                    }
                    $ids=$temp_ids;
                    $names=$temp_names;

                    $JSON=array();
                    $JSON['ids']=$ids;
                    $JSON['names']=$names;
                    $JSON['is_friends']=$is_friends;
                    $JSON['has_liked']=array();
                    $JSON['pending_friends']=$pending_friends;
                    $JSON['add_request_sent']=$add_request_sent;
                    $JSON['user_descriptions']=$user_descriptions;
                    $JSON['num_adds']=$num_friends;
                    $JSON['num_likes']=array();
                    $JSON['page_descriptions']=array();
                    $JSON['badges']=$badges;
                    echo json_encode($JSON);
                    exit();
                }
                else
                {
                    //gets users who match these parameters
                    //gets these parameters from already set array of user_ids
                    $ids=array();
                    $names=array();
                    $is_friends=array();
                    $has_liked=array();
                    $pending_friends=array();
                    $add_request_sent=array();
                    $user_descriptions=array();
                    $num_friends=array();
                    $num_likes=array();
                    $page_descriptions=array();

                    
                    //gets parameters depending on which ones the user selected
                    if($parameters[2]!=''&&$parameters[3]==''&&$parameters[4]=='')
                        $query=mysql_query("SELECT user_id, user_friends, user_bio FROM user_data WHERE high_school='$parameters[2]'");
                    else if($parameters[2]==''&&$parameters[3]!=''&&$parameters[4]=='')
                        $query=mysql_query("SELECT user_id, user_friends, user_bio FROM user_data WHERE college='$parameters[3]'");
                    else if($parameters[2]==''&&$parameters[3]==''&&$parameters[4]!='')
                        $query=mysql_query("SELECT user_id, user_friends, user_bio FROM user_data WHERE city='$parameters[4]'");
                    else if($parameters[2]!=''&&$parameters[3]!=''&&$parameters[4]=='')
                        $query=mysql_query("SELECT user_id, user_friends, user_bio FROM user_data WHERE high_school='$parameters[2]' AND college='$parameters[3]'");
                    else if($parameters[2]!=''&&$parameters[3]==''&&$parameters[4]!='')
                        $query=mysql_query("SELECT user_id, user_friends, user_bio FROM user_data WHERE high_school='$parameters[2]' AND city='$parameters[4]'");
                    else if($parameters[2]==''&&$parameters[3]!=''&&$parameters[4]!='')
                        $query=mysql_query("SELECT user_id, user_friends, user_bio FROM user_data WHERE college='$parameters[3]' AND city='$parameters[4]'");
                    else if($parameters[2]!=''&&$parameters[3]!=''&&$parameters[4]!='')
                        $query=mysql_query("SELECT user_id, user_friends, user_bio FROM user_data WHERE high_school='$parameters[2]' AND college='$parameters[3]' AND city='$parameters[4]'");

                    if($query)
                    {
                        for($x = 0; $x < mysql_num_rows($query); $x++)
                        {
                            $array=mysql_fetch_row($query);
                            $ids[]=$array[0];
                            $names[]=get_user_name($array[0]);

                            $friends=explode('|^|*|', $array[1]);

                            if($array[1]!='')
                                $num_friends[]=sizeof($friends);
                            else
                                $num_friends[]=0;
                            $user_descriptions[]=$array[2];

                            //gets profile pictures
                            $profile_pictures[]=get_profile_picture($array[0]);
                            
                            $badges[]=get_badges($array[0]);
                            
                            //determines whether current user has added this user
                            if(in_array($_SESSION['id'], $friends))
                            {
                                $is_friends[]='true';
                                $pending_friends[]='false';
                                $add_request_sent[]=0;
                            }
                            else
                            {
                                $is_friends[]='false';
                                
                                
                                //checks whether there is a pending friend request
                                $query2=mysql_query("SELECT other_user_id, user_sent FROM pending_friend_requests WHERE user_id=$array[0]");
                                if($query2&&mysql_num_rows($query2)==1)
                                {
                                    $array2=mysql_fetch_row($query2);
                                    $other_user_ids=explode('|^|*|', $array2[0]);
                                    $users_sent=explode('|^|*|', $array2[1]);
                                    
                                    $pending=false;
                                    for($y = 0; $y < sizeof($other_user_ids); $y++)
                                    {
                                        if($other_user_ids[$y]==$_SESSION['id'])
                                        {
                                            $pending=true;
                                            $add_request_sent[]=$users_sent[$y];
                                        }
                                    }
                                    
                                    if($pending)
                                        $pending_friends[]='true';
                                    else
                                        $pending_friends[]='false';
                                }
                            }
                        }
                    }
                    $JSON=array();
                    $JSON['ids']=$ids;
                    $JSON['names']=$names;
                    $JSON['is_friends']=$is_friends;
                    $JSON['has_liked']=array();
                    $JSON['pending_friends']=$pending_friends;
                    $JSON['add_request_sent']=$add_request_sent;
                    $JSON['user_descriptions']=$user_descriptions;
                    $JSON['num_adds']=$num_friends;
                    $JSON['num_likes']=array();
                    $JSON['page_descriptions']=array();
                    $JSON['profile_pictures']=$profile_pictures;
                    $JSON['badges']=$badges;
                    echo json_encode($JSON);
                    exit();

                }
            }
            else
            {
                $JSON=array();
                    $JSON['ids']=$ids;
                    $JSON['names']=$names;
                    $JSON['is_friends']=$is_friends;
                    $JSON['has_liked']=array();
                    $JSON['pending_friends']=$pending_friends;
                    $JSON['add_request_sent']=$add_request_sent;
                    $JSON['user_descriptions']=$user_descriptions;
                    $JSON['num_adds']=$num_friends;
                    $JSON['num_likes']=array();
                    $JSON['page_descriptions']=array();
                    $JSON['profile_pictures']=$profile_pictures;
                    $JSON['badges']=$badges;
                    echo json_encode($JSON);
                    exit();
            }
        }
        
        //searches for companies
        else if($num==2)
        {
            $ids=array();
            $names=array();
            $is_friends=array();
            $has_liked=array();
            $pending_friends=array();
            $add_request_sent=array();
            $user_descriptions=array();
            $num_friends=array();
            $num_likes=array();
            $page_descriptions=array();
            $profile_pictures=array();
            $badges=array();

            //if first name exists
            if($parameters[0]!='')
            {
                $query=mysql_query("SELECT page_id, likes, description FROM page_data WHERE name='$parameters[0]'");
                $query2=mysql_query("SELECT page_likes FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                if($query2)
                {
                    $array2=mysql_fetch_row($query2);
                    $pages_liked=explode('|^|*|', $array2[0]);
                }
            }

            //gets the names from name parameters if set
            if($parameters[0]!=''||$parameters[1]!='')
            {
                if($query&&mysql_num_rows($query)!=0)
                {
                    for($x = 0; $x < mysql_num_rows($query); $x++)
                    {
                        $array=mysql_fetch_row($query);

                        
                        //checks whether page is correct type
                        $query2=mysql_query("SELECT type FROM pages WHERE id=$array[0] LIMIT 1");
                        if($query2&&mysql_num_rows($query2)==1)
                        {
                            $array2=mysql_fetch_row($query2);
                            $type=$array2[0];
                            if($type=='Company')
                            {
                                $ids[]=$array[0];
                                $names[]=get_page_name($array[0]);
                                $num_likes[]=$array[1];
                                $page_descriptions[]=$array[2];
                                
                                //gets profile pictures
                                $profile_pictures[]=get_profile_picture($array[0]);
                                
                                $badges[]=get_badges($array[0]);
                                

                                //checks whether user has liked page or not
                                if(in_array($array[0], $pages_liked))
                                    $has_liked[]='true';
                                else
                                    $has_liked[]='false';
                            }
                        }
                    }
                }
                else
                {
                    $JSON=array();
                    $JSON['ids']=array();
                    $JSON['names']=array();
                    $JSON['is_friends']=array();
                    $JSON['has_liked']=array();
                    $JSON['pending_friends']=array();
                    $JSON['add_request_sent']=array();
                    $JSON['user_descriptions']=array();
                    $JSON['num_adds']=array();
                    $JSON['num_likes']=array();
                    $JSON['page_descriptions']=array();
                    $JSON['profile_pictures']=array();
                    $JSON['badges']=array();
                    echo json_encode($JSON);
                    exit();
                }
            }

            $JSON=array();
            $JSON['ids']=$ids;
            $JSON['names']=$names;
            $JSON['is_friends']=array();
            $JSON['has_liked']=$has_liked;
            $JSON['pending_friends']=array();
            $JSON['add_request_sent']=array();
            $JSON['user_descriptions']=array();
            $JSON['num_adds']=array();
            $JSON['num_likes']=$num_likes;
            $JSON['page_descriptions']=$page_descriptions;
            $JSON['profile_pictures']=$profile_pictures;
            $JSON['badges']=$badges;
            echo json_encode($JSON);
            exit();
        }

        //searches for people's pages
        else if($num==3)
        {
            $ids=array();
            $names=array();
            $is_friends=array();
            $has_liked=array();
            $pending_friends=array();
            $add_request_sent=array();
            $user_descriptions=array();
            $num_friends=array();
            $num_likes=array();
            $page_descriptions=array();
            $badges=array();

            //if first name exists
            if($parameters[0]!='')
            {
                $query=mysql_query("SELECT page_id, likes, description FROM page_data WHERE name='$parameters[0]'");
                $query2=mysql_query("SELECT page_likes FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                if($query2)
                {
                    $array2=mysql_fetch_row($query2);
                    $pages_liked=explode('|^|*|', $array2[0]);
                }
            }

            //gets the names from name parameters if set
            if($parameters[0]!=''||$parameters[1]!='')
            {
                if($query&&mysql_num_rows($query)!=0)
                {
                    for($x = 0; $x < mysql_num_rows($query); $x++)
                    {
                        $array=mysql_fetch_row($query);


                        //checks whether page is correct type
                        $query2=mysql_query("SELECT type FROM pages WHERE id=$array[0] LIMIT 1");
                        if($query2&&mysql_num_rows($query2)==1)
                        {
                            $array2=mysql_fetch_row($query2);
                            $type=$array2[0];
                            if($type=='Person')
                            {
                                $ids[]=$array[0];
                                $names[]=get_page_name($array[0]);
                                $num_likes[]=$array[1];
                                $page_descriptions[]=$array[2];
                                
                                //gets profile pictures
                                $profile_pictures[]=get_page_profile_picture($array[0]);

                                //checks whether user has liked page or not
                                if(in_array($array[0], $pages_liked))
                                    $has_liked[]='true';
                                else
                                    $has_liked[]='false';
                            }
                        }
                    }
                }
                else
                {
                    $JSON=array();
                    $JSON['ids']=array();
                    $JSON['names']=array();
                    $JSON['is_friends']=array();
                    $JSON['has_liked']=array();
                    $JSON['pending_friends']=array();
                    $JSON['add_request_sent']=array();
                    $JSON['user_descriptions']=array();
                    $JSON['num_adds']=array();
                    $JSON['num_likes']=array();
                    $JSON['page_descriptions']=array();
                    $JSON['profile_pictures']=array();
                    echo json_encode($JSON);
                    exit();
                }
            }

            $JSON=array();
            $JSON['ids']=$ids;
            $JSON['names']=$names;
            $JSON['is_friends']=array();
            $JSON['has_liked']=$has_liked;
            $JSON['pending_friends']=array();
            $JSON['add_request_sent']=array();
            $JSON['user_descriptions']=array();
            $JSON['num_adds']=array();
            $JSON['num_likes']=$num_likes;
            $JSON['page_descriptions']=$page_descriptions;
            $JSON['profile_pictures']=$profile_pictures;
            $JSON['badges']=$badges;
            echo json_encode($JSON);
            exit();
        }


        else if($num==4)
        {
            $ids=array();
            $names=array();
            $is_friends=array();
            $has_liked=array();
            $pending_friends=array();
            $add_request_sent=array();
            $user_descriptions=array();
            $num_friends=array();
            $num_likes=array();
            $page_descriptions=array();
            $profile_pictures=array();
            $badges=array();

            //if first name exists
            if($parameters[0]!='')
            {
                $query=mysql_query("SELECT page_id, likes, description FROM page_data WHERE name='$parameters[0]'");
                $query2=mysql_query("SELECT page_likes FROM user_data WHERE user_id=$_SESSION[id] LIMIT 1");
                if($query2)
                {
                    $array2=mysql_fetch_row($query2);
                    $pages_liked=explode('|^|*|', $array2[0]);
                }
            }

            //gets the names from name parameters if set
            if($parameters[0]!=''||$parameters[1]!='')
            {
                if($query&&mysql_num_rows($query)!=0)
                {
                    for($x = 0; $x < mysql_num_rows($query); $x++)
                    {
                        $array=mysql_fetch_row($query);


                        //checks whether page is correct type
                        $query2=mysql_query("SELECT type FROM pages WHERE id=$array[0] LIMIT 1");
                        if($query2&&mysql_num_rows($query2)==1)
                        {
                            $array2=mysql_fetch_row($query2);
                            $type=$array2[0];
                            if($type=='Other')
                            {
                                $ids[]=$array[0];
                                $names[]=get_page_name($array[0]);
                                $num_likes[]=$array[1];
                                $page_descriptions[]=$array[2];
                                
                                //gets profile pictures
                                $profile_pictures[]=get_page_profile_pictures($array[0]);


                                //checks whether user has liked page or not
                                if(in_array($array[0], $pages_liked))
                                    $has_liked[]='true';
                                else
                                    $has_liked[]='false';
                            }
                        }
                    }
                }
                else
                {
                    $JSON=array();
                    $JSON['ids']=array();
                    $JSON['names']=array();
                    $JSON['is_friends']=array();
                    $JSON['has_liked']=array();
                    $JSON['pending_friends']=array();
                    $JSON['add_request_sent']=array();
                    $JSON['user_descriptions']=array();
                    $JSON['num_adds']=array();
                    $JSON['num_likes']=array();
                    $JSON['page_descriptions']=array();
                    $JSON['profile_pictures']=array();
                    $JSON['badges']=array();
                    echo json_encode($JSON);
                    exit();
                }
            }

            $JSON=array();
            $JSON['ids']=$ids;
            $JSON['names']=$names;
            $JSON['is_friends']=array();
            $JSON['has_liked']=$has_liked;
            $JSON['pending_friends']=array();
            $JSON['add_request_sent']=array();
            $JSON['user_descriptions']=array();
            $JSON['num_adds']=array();
            $JSON['num_likes']=$num_likes;
            $JSON['page_descriptions']=$page_descriptions;
            $JSON['profile_pictures']=$profile_pictures;
            $JSON['badges']=$badges;
            echo json_encode($JSON);
            exit();
        }
    }
    else
    {
        $JSON=array();
        $JSON['ids']=array();
        $JSON['names']=array();
        $JSON['is_friends']=array();
        $JSON['has_liked']=array();
        $JSON['pending_friends']=array();
        $JSON['add_request_sent']=array();
        $JSON['user_descriptions']=array();
        $JSON['num_adds']=array();
        $JSON['num_likes']=array();
        $JSON['page_descriptions']=array();
        $JSON['profile_pictures']=array();
        echo json_encode($JSON);
        exit();
    }
}