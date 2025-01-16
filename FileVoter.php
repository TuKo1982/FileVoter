<?php
// FileVoter by Renaud Schweingruber

$directory = '/your/path/here/'; // Set your directory path
$dataFile = 'votes.json'; // File to store votes
$extension = 'jic'; // File extension to filter 
 
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileName = $_POST['file_name'];
    $voteType = $_POST['vote_type'];
    $votes = json_decode(file_get_contents($dataFile), true);
 
    if (!isset($votes[$fileName])) {
        $votes[$fileName] = 0;
    }
 
    if ($voteType === 'upvote') {
        $votes[$fileName]++;
    } elseif ($voteType === 'downvote') {
        $votes[$fileName]--;
    }
 
    file_put_contents($dataFile, json_encode($votes));
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
 
$files = array_diff(scandir($directory), array('..', '.')); // Retrieve files from the directory

$FilteredFiles = array_filter($files, function($file) {
    return strtolower(pathinfo($file, PATHINFO_EXTENSION)) === $extension;
});
 
$votes = json_decode(file_get_contents($dataFile), true);
$filesWithVotes = [];
 
foreach ($Filteredfiles as $file) {
    $filesWithVotes[$file] = isset($votes[$file]) ? $votes[$file] : 0;
}
 
arsort($filesWithVotes); // Sort files by votes
 
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Voting</title>
</head>
<body>
    <h1>File Voting</h1>
    <ul>
        <?php foreach ($filesWithVotes as $file => $voteCount): ?>
            <li>
                <?php echo htmlspecialchars($file); ?> - Votes: <?php echo $voteCount; ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($file); ?>">
                    <button type="submit" name="vote_type" value="upvote">Upvote</button>
                    <button type="submit" name="vote_type" value="downvote">Downvote</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
