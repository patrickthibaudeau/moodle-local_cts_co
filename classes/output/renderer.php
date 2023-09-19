<?php

namespace local_cts_co\output;

/**
 * Description of renderer
 *
 * @author patrick
 */
class renderer extends \plugin_renderer_base {

    /**
     * Used with root/index.php
     * @param \templatable $dashboard
     * @return type
     */
    public function render_dashboard(\templatable $dashboard) {
        $data = $dashboard->export_for_template($this);
        return $this->render_from_template('local_cts_co/dashboard', $data);
    }

    /**
     * Process details
     * @param \templatable $details
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_details(\templatable $details) {
        $data = $details->export_for_template($this);
        return $this->render_from_template('local_cts_co/details', $data);
    }

}
