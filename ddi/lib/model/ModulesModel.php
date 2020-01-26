<?

class ModulesModel extends Model {

    public function __construct() {
        
    }

    public function getAvaibleMenu() {

        $menu_part_1 = Array("main", "group", "user");
        $menu_part_2 = $GLOBALS['conf']['ddi']['modules'];
        $menu_part_3 = Array("reporter");

        $menu = array_merge($menu_part_1, $menu_part_2, $menu_part_3);

        $menu_translated = Array();
        foreach ($menu as $module) {
            $menu_translated[] = $GLOBALS['lang'][$module];
        }

        $avaible_menu = Array();
        $avaible_menu['menu'] = $menu;
        $avaible_menu['menu_translated'] = $menu_translated;

        return $avaible_menu;
    }

}

?>
