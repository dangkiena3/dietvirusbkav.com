<?php
/**
 * Webhomeschool Component
 * @package ABlog
 * @subpackage Controllers
 *
 * @copyright (C) 2013 Webhomeschool. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.webhomeschool.de
 * */
// No direct access
defined('_JEXEC') or die('Restricted access');
// Load the base JModel class
/**
 * Revue Model
 */
class ABlogModelComments extends JModelLegacy {

    protected $creator_id = '';
    private $total_comments = 0;
    private $total_comment_answers = 0;

    function getComments($post_id) {
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comments');
        $query1 = 'SELECT * FROM' . $table . 'WHERE post_id=' . (int)$post_id . ' AND published=1 ORDER BY id';
        $query2 = 'SELECT * FROM' . $table . 'WHERE post_id=' . (int)$post_id . ' AND published=1';
        $params = JComponentHelper::getParams('com_ablog');
        $limitstarter = aBlogHelper::getComLimitStarter();
        $limit = $params->get('ablog_frontend_comments_limit');
        $db->setQuery($query1,$limitstarter,$limit);
        return $db->loadObjectList();
    }
    //counts all published comments
    function getTotalComments()
    {
        $this->total_comments;
    }
    //counts all published comment answers

    private function createTotalRows($db,$query){
        $db->setQuery($query);
        $db->execute();
        return $db->getNumRows();
    }

    function store($data) {
        JSession::checkToken() or exit();
        $db = $this->getDBO();
        $date = JFactory::getDate();
        $created_date = $date->toSql();
        $table = $db->quoteName('#__ablog_comments');
        $table_post = $db->quoteName('#__ablog_posts');
        $select_query = "SELECT creator_id FROM". $table_post . " WHERE id=" .(int)$data['post_id'];
        $content = $data['ablog_front_content'];
        $post_creator = $this->getPostCreatorByPostId()->creator_id;

        $query = "INSERT INTO" . $table .
                "(id, creator, content, post_id, created_date, email_adress,post_creator) VALUES('', " .
                $db->quote($data['creator']) . ", " . $db->quote($content) . "," . (int)$data['post_id'] .
                "," . $db->quote($date) . "," . $db->quote($data['email_adress']) .",". $db->quote($post_creator) . ")";
        $db->setQuery($query);
        return $db->query();
    }

    function storeAndPost($data) {
        JSession::checkToken() or exit();
        $date = JFactory::getDate();
        $created_date = $date->toSql();
        $db = $this->getDBO();
        $content = $data['ablog_front_content'];
        $table = $db->quoteName('#__ablog_comments');

        $query = "INSERT INTO" . $table .
                "(id, creator, content, post_id, created_date, email_adress, published) VALUES('', '" .
                $db->quote($data['creator']) . "', '" . $db->quote($content) . "','" . (int)$data['post_id'] .
                "','" . $db->quote($date) . "','" . $db->quote($data['email_adress']) . "','1'" . ")";
        $db->setQuery($query);
        return $db->query();
    }

    function storeCommentAnswer() {
        JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
        $data = JFactory::getApplication()->input->getArray($_POST);
        $date = JFactory::getDate();
        $date = $date->toSql();
        $data['content'] = $data['ablog_front_content'];
        $db = $this->getDbo();
        $table = $db->quoteName('#__ablog_comment_answers');
        $query = "INSERT INTO" . $table .
                "(id, comment_id, post_id, creator, email_adress, content, created_date) VALUES ('', " .
                (int)$data['comment_id'] . ", " . (int)$data['post_id'] . ", " . $db->quote($data['creator']). ", " . $db->quote($data['email_adress']) . ", " . $db->quote($data['content']) . ", " . $db->quote($date) . ")";
        $db->setQuery($query);
        return $db->execute();
    }

    function storePublishCommentAnswer() {
        JSession::checkToken() or jexit(JText::_('INVALID TOKEN'));
        $data = JFactory::getApplication()->input->getArray($_POST);
        $date = JFactory::getDate();
        $date = $date->toSql();
        $data['published'] = 1;
        $db = $this->getDBO();
        $table = $db->quoteName('#__ablog_comment_answers');
        $query = "INSERT INTO" . $table .
                "(id, comment_id, post_id, creator, content, published, created_date) VALUES ('', " .
                (int)$data['comment_id'] . ", " . (int)$data['post_id'] . ", " . $db->quote($data['creator']) . ", " . $db->quote($data['content']) .
                ", " . (int)($data['published']) . ", " . $db->quote($date) . ")";
        $db->setQuery($query);
        return $db->query();
    }

    function getCommentAnswersForView($post_id, $comment_id) {
        $db = JFactory::getDBO();
        $params = JComponentHelper::getParams('com_ablog');
        $limit = $params->get('ablog_frontend_comment_answers_limit');
        $table = $db->quoteName('#__ablog_comment_answers');
        $query = 'SELECT * FROM' . $table .
                ' WHERE post_id=' . (int)$post_id . ' AND comment_id=' . (int)$comment_id . ' AND published=1';

        $db->setQuery($query,0,$limit);
        return $db->loadObjectList();
    }

    function getCommentByPostId($post_id, $comment_id) {
        $db = JFactory::getDbo();
        $table = $db->quoteName('#__ablog_comments');
        $query = 'SELECT * FROM ' . $table .
                ' WHERE post_id=' . (int)$post_id . ' AND id=' . (int)$comment_id;
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    private function getPostCreatorByPostId(){
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $table_post = $db->quoteName('#__ablog_posts');
        $id = $app->input->get('id','','int');
        $query = "SELECT creator_id FROM ".$table_post. " WHERE id=" . (int)$id;
        $db->setQuery($query);
        return $db->loadObject();
    }

    /*function informBlogger() {
        $this->creator_id = $this->getPostCreatorByPostId();
        $user = JFactory::getUser('66');
        $user_params = $user->getParams();
        /*$user_email = $user_params->get('email_adress');
        $mailer = JFactory::getMailer();
        $mailer->setSender($user_email);
        $mailer->setSubject('Comment Answer for Post ' . (int)$post_id);
        $content = 'This is an Answer for the post with the post_id: ' . (int)$post_id . ' with this content: ';
        $db = JFactory::getDbo();
        $table_posts = $db->quoteName('#__ablog_posts');
        $query = 'SELECT * content FROM' . $table_posts . ' WHERE id=' . (int)$post_id;
        $db->setQuery($query);
        $row_content = $db->loadObject();
        $content .= $row_content->content;
        $mailer->setBody($content);
        if ($mailer->Send() !== true) {
            $app = JFactory::getApplication();
            $app->enqueueMessage('An Error accured, please inform the site administrator', 'warning');
        }
    }*/
}
