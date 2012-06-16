defined('_VALID_MOS') or die();
require_once dirname(__file__)."/../Predicate.php";
class File_Archive_Predicate_And extends File_Archive_Predicate {
var $preds;
function File_Archive_Predicate_And() {
$this->preds = func_get_args();
}
function addPredicate($pred) {
$this->preds[] = $pred;
}
function isTrue(&$source) {
foreach($this->preds as $p) {
if(!$p->isTrue($source)) {
return false;
}
}
return true;
}
}

?>
