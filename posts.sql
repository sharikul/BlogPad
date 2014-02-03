CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL,
  `post` longtext NOT NULL,
  `description` longtext NOT NULL,
  `author` mediumtext NOT NULL,
  `date` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `slug` varchar(50) NOT NULL,
  `categories` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `posts` (`id`, `title`, `post`, `description`, `author`, `date`, `updated`, `slug`, `categories`) VALUES (1, 'An example of a dynamic post.', 'Now that you''ve successfully set up BlogPad, it''s time to introduce you to the different post types that BlogPad provides. This is a **dynamic post**, and is located within the database whose credentials you''ve provided.\r\n\r\nYou''re probably viewing the contents of this post in the web editor, _or_ you''re viewing the post in your browser. Additionally, you may also be viewing this post in a MySQL interface, such as _phpMyAdmin_.', 'This is an example of a dynamic post. Feel free to edit it.', 'admin', '2014-02-03 19:29:50', '2014-02-03 19:30:17', 'example-dynamic-post', 'a:1:{i:0;s:7:"BlogPad";}');