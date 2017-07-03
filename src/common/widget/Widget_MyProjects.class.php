<?php
/**
 * Copyright (c) Enalean, 2016 - 2017. All Rights Reserved.
 * Copyright (c) Xerox Corporation, Codendi Team, 2001-2009. All rights reserved
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once('Widget.class.php');
require_once('common/rss/RSS.class.php');
require_once 'common/templating/TemplateRendererFactory.class.php';
require_once 'common/mail/MassmailFormPresenter.class.php';

/**
* Widget_MyProjects
*
* PROJECT LIST
*/
class Widget_MyProjects extends Widget {

    public function __construct()
    {
        parent::__construct('myprojects');
    }

    function getTitle() {
        return $GLOBALS['Language']->getText('my_index', 'my_projects');
    }

    public function getContent()
    {
        $GLOBALS['HTML']->includeFooterJavascriptFile("/scripts/ckeditor-4.3.2/ckeditor.js");
        $GLOBALS['HTML']->includeFooterJavascriptFile('/scripts/tuleap/tuleap-ckeditor-toolbar.js');
        $GLOBALS['HTML']->includeFooterJavascriptFile('/scripts/widgets/contact-modal.js');

        $html = '';
        $display_privacy = ForgeConfig::get('sys_display_project_privacy_in_service_bar');
        $user = UserManager::instance()->getCurrentUser();

        $order = 'groups.group_name';
        if ($display_privacy) {
            $order = 'access, groups.group_name';
        }
        $result = db_query("SELECT groups.group_id, groups.group_name, groups.unix_group_name, groups.status, groups.access, user_group.admin_flags".
                           " FROM groups".
                           " JOIN user_group USING (group_id)".
                           " WHERE user_group.user_id = ". db_ei($user->getId()).
                           " AND groups.status = 'A'".
                           " ORDER BY $order");
        $rows=db_numrows($result);
        if (!$result || $rows < 1) {
            $html .= '<div class="empty-pane">';
            $html .= '<svg class="empty-pane-icon" xmlns="http://www.w3.org/2000/svg" width="239" height="287" viewBox="0 0 239 287">';
            $html .= '    <defs>';
            $html .= '        <filter id="my-projects-empty-a" width="112.6%" height="900%" x="-6.3%" y="-400%" filterUnits="objectBoundingBox">';
            $html .= '            <feGaussianBlur in="SourceGraphic" stdDeviation="4"/>';
            $html .= '        </filter>';
            $html .= '        <filter id="my-projects-empty-b" width="183.3%" height="134.9%" x="-41.6%" y="-17.5%" filterUnits="objectBoundingBox">';
            $html .= '            <feGaussianBlur in="SourceGraphic" stdDeviation="3"/>';
            $html .= '        </filter>';
            $html .= '    </defs>';
            $html .= '    <g fill="none" fill-rule="evenodd" transform="translate(8)">';
            $html .= '        <rect class="empty-project-box-inside" width="42" height="8" x="96" y="182" fill="#C1C1C1"/>';
            $html .= '        <path class="empty-project-box-icons" fill="#000" d="M50.9265705,1.5010844 C51.7072913,1.61080756 52.7513129,2.22015793 53.2318923,2.85790837 L63.6776941,16.7199555 C64.1582735,17.357706 64.0307574,18.2650302 63.393007,18.7456096 L47.2206186,30.9323784 C46.5828682,31.4129578 45.6755439,31.2854417 45.1949645,30.6476912 L30.6869065,11.394848 C30.2063271,10.7570975 30.3338432,9.8497733 30.9715936,9.36919388 L41.7531858,1.24468138 C42.3909363,0.76410196 43.5624739,0.466128114 44.3431947,0.575851273 L50.9265705,1.5010844 Z M43.3714276,2.5910119 L46.7808213,7.11543007 L51.3052394,3.70603643 C51.0788932,3.55588019 50.8164478,3.43292656 50.6687439,3.41216812 L44.0642675,2.48396951 C43.9165636,2.46321107 43.6303943,2.50906195 43.3714276,2.5910119 Z M61.8473054,17.4955433 L52.5621483,5.17372361 L47.5564091,8.9458187 C46.9186586,9.42639812 46.0113344,9.29888201 45.530755,8.66113158 L41.7586599,3.65539233 L32.5172951,10.6192602 L46.4450308,29.1019897 L61.8473054,17.4955433 Z M41.6911344,16.3841651 C41.5279188,16.1675706 41.5694356,15.8721627 41.7860301,15.708947 L50.2572812,9.32540152 C50.4738756,9.16218586 50.7692835,9.20370273 50.9324992,9.42029722 L51.5128215,10.190411 C51.6760372,10.4070054 51.6345203,10.7024133 51.4179258,10.865629 L42.9466748,17.2491745 C42.7300803,17.4123902 42.4346724,17.3708733 42.2714567,17.1542788 L41.6911344,16.3841651 Z M52.5785704,12.4058564 C52.7951649,12.2426408 53.0905728,12.2841577 53.2537885,12.5007521 L53.8341108,13.2708659 C53.9973264,13.4874604 53.9558096,13.7828683 53.7392151,13.9460839 L45.267964,20.3296294 C45.0513696,20.4928451 44.7559617,20.4513282 44.592746,20.2347337 L44.0124237,19.46462 C43.849208,19.2480255 43.8907249,18.9526176 44.1073194,18.789402 L52.5785704,12.4058564 Z M54.8998597,15.4863114 C55.1164542,15.3230957 55.4118621,15.3646126 55.5750778,15.5812071 L56.1554001,16.3513208 C56.3186157,16.5679153 56.2770989,16.8633232 56.0605044,17.0265388 L47.5892533,23.4100844 C47.3726588,23.5733 47.077251,23.5317831 46.9140353,23.3151887 L46.333713,22.5450749 C46.1704973,22.3284804 46.2120142,22.0330726 46.4286087,21.8698569 L54.8998597,15.4863114 Z M205.07944,138.531119 C204.926913,138.873699 204.560274,139.12095 204.217493,139.276201 L196.904688,142.7934 C195.644628,143.395438 193.617092,143.416321 192.360966,142.857058 L176.830674,135.942527 C176.316804,135.713737 175.65873,135.232602 175.944717,134.590265 C176.097243,134.247685 176.463882,134.000434 176.806664,133.845183 L184.119469,130.327984 C185.379529,129.725946 187.407065,129.705063 188.663191,130.264327 L204.193482,137.178858 C204.707352,137.407647 205.365427,137.888782 205.07944,138.531119 Z M202.369613,131.44095 L201.352771,133.724816 L189.476665,128.437234 C187.692395,127.642825 185.028675,127.671224 183.262362,128.526768 L175.935283,132.037612 L175.82578,132.09148 C175.876622,131.977287 175.919546,131.842464 175.970388,131.728271 L182.071445,118.025073 C182.853143,116.26935 184.936715,115.469542 186.692438,116.25124 L191.26017,118.284925 C193.015893,119.066623 193.815701,121.150196 193.034003,122.905918 L192.830635,123.362691 L200.59578,126.819957 C202.351503,127.601655 203.151311,129.685228 202.369613,131.44095 Z M110.153626,186.287239 C110.384499,186.582743 110.430683,187.022543 110.406835,187.398087 L110.079183,195.506136 C110.019189,196.901342 109.085757,198.701355 108.002241,199.54789 L94.6060422,210.014148 C94.1627856,210.360458 93.4290219,210.715624 92.9961344,210.161553 C92.7652611,209.866049 92.719078,209.426249 92.7429259,209.050705 L93.0705774,200.942656 C93.1305719,199.547449 94.0640034,197.747437 95.1475195,196.900902 L108.543718,186.434644 C108.986975,186.088334 109.720739,185.733168 110.153626,186.287239 Z M102.621192,185.351241 L104.160347,187.32127 L93.9161951,195.324879 C92.3771098,196.527344 91.1516442,198.892601 91.0778102,200.853816 L90.7378461,208.971485 L90.734001,209.09346 C90.6570432,208.994958 90.558153,208.893764 90.4811953,208.795262 L81.2462621,196.975087 C80.0630363,195.460627 80.3350253,193.245454 81.8494853,192.062228 L85.7895438,188.983917 C87.3040038,187.800691 89.5191766,188.07268 90.7024024,189.58714 L91.0102335,189.981146 L97.7083329,184.748017 C99.2227929,183.564792 101.437966,183.836781 102.621192,185.351241 Z M159.246213,60.2675414 C165.709956,61.0611892 170.476881,65.5089944 169.899497,70.2114089 C169.322113,74.9138235 163.620765,78.0762864 157.157022,77.2826385 C156.143427,77.1581848 155.175307,76.9381136 154.265196,76.6576979 C152.765669,77.4687212 151.116007,77.9914411 149.384714,78.2174023 C148.921792,78.2786303 148.429718,78.3025453 147.923068,78.3078037 L147.873219,78.3016831 C147.623974,78.2710797 147.415829,78.0431209 147.400664,77.754523 C147.372963,77.4306523 147.613458,77.2577795 147.82276,77.06421 C148.560566,76.4126614 149.371252,75.8543954 150.105545,74.5446097 C147.605485,72.6521604 146.173222,70.0306125 146.503738,67.338771 C147.081122,62.6363564 152.78247,59.4738935 159.246213,60.2675414 Z M170.519793,81.3690685 C170.915408,82.8175896 171.566957,83.5553954 172.125223,84.3660817 C172.281479,84.6045362 172.473009,84.8304548 172.367779,85.1380037 C172.26459,85.4289363 171.988822,85.6143449 171.727042,85.5484686 C171.236714,85.4207966 170.765042,85.2785486 170.330683,85.1071483 C168.705482,84.4690616 167.231279,83.5627792 165.972499,82.4130784 C165.021584,82.4649888 164.02898,82.4443133 163.015386,82.3198595 C160.007834,81.9505787 157.351539,80.7810859 155.441799,79.1635206 C155.919592,79.2559198 156.414,79.3503591 156.895873,79.4095257 C160.468379,79.8481742 163.967983,79.2321291 166.772016,77.6873379 C169.795846,76.0008663 171.661184,73.4468766 172.026384,70.4725578 C172.132476,69.6085099 172.103597,68.7447565 171.954323,67.8999542 C174.572738,69.7900688 176.098578,72.4737069 175.757861,75.2486299 C175.425304,77.9570878 173.403415,80.1376523 170.519793,81.3690685 Z M158.985064,62.3944285 C153.800777,61.7578781 149.054992,64.1437283 148.630626,67.5999199 C148.406201,69.4277135 149.419543,71.3400187 151.395454,72.8307742 L152.892983,73.9591892 L152.140033,75.283551 C152.513458,75.1270001 152.888924,74.9538328 153.249813,74.7620091 L154.044177,74.3366735 L154.904439,74.6109686 C155.716893,74.8625266 156.554123,75.0496597 157.418171,75.1557514 C162.602458,75.7923018 167.348243,73.4064516 167.77261,69.95026 C168.196977,66.4940684 164.169352,63.030979 158.985064,62.3944285 Z M26.2286955,69.2559408 C26.2457518,69.2794167 26.2713362,69.3146307 26.2766545,69.3466348 C28.297889,74.2267733 26.9768494,79.6162735 22.6455334,82.7631588 C20.3566266,84.4261469 17.4814805,85.1341415 14.6633873,84.7605102 L14.249318,87.3748407 C14.2108,87.6180342 14.0826892,87.8366504 13.8831435,87.9816289 C13.4723141,88.2801139 12.8845964,88.1870286 12.5861113,87.7761992 L8.76550261,82.5175824 C8.46701756,82.106753 8.5601029,81.5190353 8.97093233,81.2205502 L14.2295491,77.3999415 C14.6403785,77.1014565 15.2280962,77.1945418 15.5265813,77.6053713 C15.6715597,77.804917 15.7258441,78.0524217 15.6873261,78.2956153 L15.2475781,81.0720748 C17.1014253,81.3033718 18.9950805,80.824253 20.5092804,79.7241223 C22.5986415,78.2061126 23.7508195,75.7190743 23.5461741,73.1417833 C23.495288,72.4793265 23.3120744,71.9309454 23.1704943,71.3164476 C23.1097902,71.1094751 23.1504167,70.9185518 23.3264865,70.7906296 L25.5801794,69.1532259 C25.7914631,68.9997193 26.083717,69.056395 26.2286955,69.2559408 Z M19.6996295,59.6523501 L23.5202382,64.9109669 C23.8187233,65.3217963 23.725638,65.909514 23.3148085,66.2079991 L18.0561918,70.0286078 C17.6453624,70.3270928 17.0576446,70.2340075 16.7591596,69.823178 C16.6141811,69.6236323 16.5598967,69.3761276 16.5984148,69.132934 L17.0413726,66.3362083 C15.1757874,66.1134395 13.2906604,66.6042963 11.7764605,67.704427 C9.68709938,69.2224367 8.53492134,71.709475 8.7395668,74.286766 C8.79045282,74.9492228 8.9736665,75.4976039 9.11524655,76.1121017 C9.17595066,76.3190742 9.13532412,76.5099975 8.95925436,76.6379197 L6.62339559,78.3350204 C6.41211188,78.488527 6.11985794,78.4318513 5.97487949,78.2323055 L5.91518248,78.1501397 C3.89715782,73.249735 5.26193951,67.8463884 9.59325552,64.6995031 C11.8939003,63.0279868 14.8010505,62.314674 17.6106156,62.6765673 L18.0364228,60.0537086 C18.0749409,59.8105151 18.2030516,59.5918989 18.4025973,59.4469204 C18.8134268,59.1484353 19.4011445,59.2415207 19.6996295,59.6523501 Z M213.037125,42.34568 L212.261372,42.8888678 C212.059676,43.0300967 211.795888,42.9835837 211.654659,42.781888 L206.592149,35.5518732 C206.45092,35.3501775 206.497433,35.0863894 206.699128,34.9451605 L213.929143,29.8826497 C214.130839,29.7414208 214.394627,29.7879338 214.535856,29.9896295 L215.079044,30.7653821 C215.220273,30.9670778 215.17376,31.2308659 214.972064,31.3720948 L208.874648,35.6415514 L213.144105,41.7389673 C213.285333,41.940663 213.23882,42.2044511 213.037125,42.34568 Z M223.31353,29.6470425 L215.39147,45.807098 C215.285987,46.0196897 215.025321,46.109722 214.825919,46.0065646 L214.047712,45.6381546 C213.83512,45.5326715 213.745088,45.2720058 213.850571,45.0594141 L221.772631,28.8993586 C221.878114,28.686767 222.138779,28.5967346 222.338182,28.699892 L223.116389,29.068302 C223.32898,29.1737851 223.419013,29.4344509 223.31353,29.6470425 Z M230.464972,39.7612961 L223.234957,44.823807 C223.033262,44.9650358 222.769474,44.9185229 222.628245,44.7168272 L222.085057,43.9410745 C221.943828,43.7393788 221.990341,43.4755907 222.192037,43.3343619 L228.289453,39.0649052 L224.019996,32.9674893 C223.878767,32.7657936 223.92528,32.5020055 224.126976,32.3607767 L224.902728,31.8175888 C225.104424,31.67636 225.368212,31.7228729 225.509441,31.9245686 L230.571952,39.1545834 C230.713181,39.3562791 230.666668,39.6200672 230.464972,39.7612961 Z M158.206197,168.260575 C158.538925,169.226887 158.449533,170.791347 157.99575,171.721741 L148.132387,191.944631 C147.678604,192.875024 146.555593,193.261708 145.6252,192.807925 L122.031828,181.300668 C121.101434,180.846885 120.714751,179.723874 121.168534,178.793481 L134.867648,150.706133 C135.321432,149.77574 136.444443,149.389056 137.374836,149.842839 L153.103751,157.514343 C154.034144,157.968127 155.067764,159.145903 155.400491,160.112215 L158.206197,168.260575 Z M152.501073,160.17575 L149.281781,166.776277 L155.882308,169.995569 C155.922533,169.645769 155.910095,169.270284 155.847146,169.087468 L153.032448,160.912991 C152.9695,160.730175 152.748129,160.426631 152.501073,160.17575 Z M146.159382,190.286955 L154.926815,172.311052 L147.624105,168.749282 C146.693711,168.295499 146.307028,167.172488 146.760811,166.242095 L150.322581,158.939384 L136.840654,152.363809 L123.689504,179.327663 L146.159382,190.286955 Z M90.9940499,64.6410463 C91.5135245,65.9267911 91.2608961,67.3348993 90.4701244,68.349542 C93.0570694,74.9088699 89.0064681,78.2306316 86.3230177,80.4523345 C83.809501,82.521119 83.0434258,83.3361972 83.7531305,85.0927781 L83.9433607,85.5636142 C85.2170242,85.7441709 86.3769041,86.5815887 86.8963787,87.8673335 C87.6719324,89.7868961 86.7437741,91.9734999 84.8242115,92.7490535 C82.9046489,93.5246071 80.7180452,92.5964489 79.9424915,90.6768863 C79.4230169,89.3911415 79.6756454,87.9830333 80.4664171,86.9683906 L74.4668512,72.118944 C73.1931876,71.9383873 72.0333077,71.1009695 71.5138331,69.8152247 C70.7382795,67.8956621 71.6664377,65.7090583 73.5860003,64.9335047 C75.5055629,64.1579511 77.6921667,65.0861093 78.4677203,67.0056719 C78.9871949,68.2914167 78.7345665,69.6995249 77.9437948,70.7141676 L81.580117,79.714381 C82.3207666,78.8885102 83.1742757,78.1855625 83.9518725,77.5554156 C86.8884856,75.1261027 88.4861203,73.5537495 86.9931808,69.7543184 C85.7195172,69.5737617 84.5596373,68.7363439 84.0401627,67.4505991 C83.2646091,65.5310365 84.1927673,63.3444327 86.1123299,62.5688791 C88.0318925,61.7933255 90.2184963,62.7214837 90.9940499,64.6410463 Z M85.1579069,88.5697217 C84.7701301,87.6099404 83.6768282,87.1458613 82.7170469,87.5336381 C81.7572656,87.9214149 81.2931865,89.0147168 81.6809633,89.9744981 C82.0687401,90.9342794 83.162042,91.3983585 84.1218233,91.0105817 C85.0816046,90.6228049 85.5456837,89.529503 85.1579069,88.5697217 Z M76.7292485,67.7080601 C76.3414717,66.7482788 75.2481698,66.2841997 74.2883885,66.6719765 C73.3286072,67.0597533 72.8645281,68.1530552 73.2523049,69.1128365 C73.6400817,70.0726178 74.7333836,70.5366969 75.6931649,70.1489201 C76.6529462,69.7611433 77.1170253,68.6678414 76.7292485,67.7080601 Z M89.2555781,65.3434345 C88.8678013,64.3836532 87.7744994,63.9195741 86.8147181,64.3073509 C85.8549368,64.6951277 85.3908577,65.7884296 85.7786345,66.7482109 C86.1664113,67.7079922 87.2597132,68.1720713 88.2194945,67.7842945 C89.1792758,67.3965177 89.6433549,66.3032158 89.2555781,65.3434345 Z M78.3351215,153.237078 L81.6660466,158.567672 L79.0007494,160.233135 L75.6698244,154.90254 L78.3351215,153.237078 Z M79.0021423,145.40829 L85.6639924,156.069478 L82.9986952,157.734941 L76.3368451,147.073752 L79.0021423,145.40829 Z M97.1579124,150.740277 L97.9906437,152.072925 L76.6682662,165.396626 L66.675491,149.404842 L68.0081396,148.572111 L77.1681835,163.231246 L97.1579124,150.740277 Z M84.6655506,145.575393 L89.6619382,153.571285 L86.996641,155.236747 L82.0002534,147.240855 L84.6655506,145.575393 Z M86.1653026,139.079253 L93.659884,151.073091 L90.9945868,152.738553 L83.5000054,140.744716 L86.1653026,139.079253 Z M129.067927,21.3909598 C129.157847,22.6768837 128.203171,23.4719787 126.987388,23.5569945 C126.250905,23.6084945 125.485564,23.4153173 124.922719,22.9260432 L125.517125,21.8507089 C125.853197,22.1208929 126.348845,22.3211814 126.793073,22.2901179 C127.202231,22.2615069 127.620872,22.0325272 127.588174,21.5649184 C127.542396,20.9102663 126.79177,20.927513 126.314923,20.9960995 L125.9652,20.3627013 C126.348445,19.7955227 126.692531,19.1723455 127.16333,18.6812762 L127.162512,18.669586 C126.788425,18.6957447 126.404283,18.7461013 126.030196,18.77226 L126.073521,19.3918415 L124.834358,19.4784923 L124.710105,17.7015792 L128.602947,17.4293651 L128.674883,18.4581043 L127.658321,19.880138 C128.454645,20.0124117 129.011522,20.5843347 129.067927,21.3909598 Z M128.57876,14.0595584 L128.708736,15.918303 L124.476877,16.2142234 C124.427092,16.0062518 124.377307,15.7982803 124.362593,15.5878564 C124.211363,13.4251661 126.830463,12.9130941 126.761797,11.9311159 C126.734003,11.5336485 126.473794,11.3403912 126.088017,11.3673674 C125.67886,11.3959785 125.364367,11.7703913 125.188522,12.1116142 L124.146624,11.4913754 C124.475996,10.6577743 125.23905,10.1462685 126.127506,10.0841416 C127.214697,10.0081178 128.194874,10.585683 128.275803,11.7430146 C128.396787,13.4731668 125.886986,14.0363327 125.915677,14.9506221 L127.400335,14.8468047 L127.351287,14.1453916 L128.57876,14.0595584 Z M145.311046,16.6369372 L145.467999,18.8814589 C145.481895,19.0801926 145.318804,19.2678078 145.12007,19.2817046 L130.904766,20.2757356 C130.694342,20.2904498 130.518417,20.126541 130.50452,19.9278073 L130.347568,17.6832855 C130.332853,17.4728616 130.485072,17.2977541 130.695496,17.2830398 L144.910801,16.2890089 C145.109534,16.2751121 145.296332,16.4265132 145.311046,16.6369372 Z M128.116324,7.27841468 L128.197252,8.43574623 L124.281029,8.70959521 L124.200101,7.55226366 L125.450954,7.46479548 C125.38474,6.51788785 125.330216,5.57016276 125.264001,4.62325513 L125.254192,4.48297252 L125.230812,4.48460744 C125.120203,4.75078429 124.898661,4.94248674 124.690443,5.15675218 L123.798311,4.32633527 L125.284363,2.73050312 L126.523526,2.64385239 L126.85378,7.36670032 L128.116324,7.27841468 Z M144.892507,10.6515457 L145.049459,12.8960675 C145.063356,13.0948012 144.900265,13.2824164 144.701531,13.2963132 L130.486226,14.2903441 C130.275802,14.3050584 130.099877,14.1411496 130.085981,13.9424159 L129.929028,11.6978941 C129.914314,11.4874702 130.066533,11.3123627 130.276957,11.2976484 L144.492261,10.3036175 C144.690995,10.2897207 144.877793,10.4411218 144.892507,10.6515457 Z M144.473968,4.66615429 L144.63092,6.91067608 C144.644817,7.10940978 144.481725,7.29702493 144.282992,7.31092175 L130.067687,8.30495268 C129.857263,8.31966696 129.681338,8.15575813 129.667441,7.95702443 L129.510489,5.71250264 C129.496592,5.51376894 129.647993,5.32697124 129.858417,5.31225697 L144.073722,4.31822603 C144.272456,4.30432922 144.460071,4.46742059 144.473968,4.66615429 Z M128.961205,112.793748 L119.604883,118.640227 C119.343869,118.803326 119.017629,118.728007 118.85453,118.466994 L118.227225,117.463097 C118.064126,117.202084 118.139444,116.875843 118.400458,116.712744 L126.29109,111.78213 L121.360476,103.891498 C121.197377,103.630484 121.272695,103.304244 121.533708,103.141145 L122.537606,102.51384 C122.798619,102.350741 123.124859,102.426059 123.287958,102.687073 L129.134437,112.043395 C129.297537,112.304408 129.222218,112.630649 128.961205,112.793748 Z M144.840844,124.311774 L144.599825,125.355744 C144.532038,125.64936 144.250947,125.825005 143.957331,125.757219 L128.297793,122.141929 C128.004177,122.074143 127.828532,121.793052 127.896318,121.499435 L128.137337,120.455466 C128.205124,120.16185 128.486215,119.986204 128.779832,120.053991 L144.439369,123.66928 C144.732986,123.737067 144.908631,124.018158 144.840844,124.311774 Z" opacity=".9"/>';
            $html .= '        <path class="empty-project-box-shadow" fill="#333" d="M96.043956,276.746611 C149.087568,276.746611 189.304029,277.552935 189.304029,276.288843 C189.304029,275.718529 192.087912,274 186.520147,274 C180.952381,274 125.156176,274 96.043956,274 C80.8536468,274 10.2075702,274 5.1037851,274 C9.75629139e-09,274 4.12097435e-16,275.386753 4.12097435e-16,276.288843 C4.12097435e-16,277.552935 43.0003438,276.746611 96.043956,276.746611 Z" filter="url(#my-projects-empty-a)" opacity=".15"/>';
            $html .= '        <path class="empty-project-box" fill="#ECECEC" d="M46,173.999811 C46,172.895346 46.9008006,172 48.0081404,172 L187.99186,172 C189.100925,172 190,172.890441 190,173.999811 L190,269.997287 C190,272.760209 187.767003,275 185.001376,275 L50.9986238,275 C48.2379601,275 46,272.752909 46,269.997287 L46,173.999811 Z M96,183.998101 L96,188.001899 C96,189.113294 96.8943077,190 97.9974922,190 L136.002508,190 C137.10334,190 138,189.10542 138,188.001899 L138,183.998101 C138,182.886706 137.105692,182 136.002508,182 L97.9974922,182 C96.8966603,182 96,182.89458 96,183.998101 Z"/>';
            $html .= '        <path class="empty-project-box-top-shadow" fill="#000" d="M51.5,165 L41.5,192 C41.5,192 39,217.5 41.5,216.5 C44,215.5 62,169 62,169 L51.5,165 Z" filter="url(#my-projects-empty-b)" opacity=".196"/>';
            $html .= '        <path class="empty-project-box-top" fill="#ECECEC" d="M4.90336505,268.495082 C3.37068338,267.875838 2.62777311,266.137354 3.25055476,264.595915 L59.8190792,124.583904 C60.438939,123.049697 62.1845186,122.308216 63.7166002,122.927217 L78.0360971,128.712669 C79.0584283,129.125718 79.5491579,130.297222 79.1382247,131.314318 L21.8229232,273.174667 C21.4092803,274.198469 20.2354351,274.68964 19.2228619,274.280534 L4.90336505,268.495082 Z"/>';
            $html .= '    </g>';
            $html .= '</svg>';
            $html .= '<p class="empty-pane-text">' . $GLOBALS['Language']->getText('my_index', 'not_member') . '</p>';
            $html .= '</div>';
        } else {
            $html .= '<table cellspacing="0" class="tlp-table widget_my_projects">';
            $i     = 0;
            $prevIsPublic = -1;
            $token = new CSRFSynchronizerToken('massmail_to_project_members.php');
            while ($row = db_fetch_array($result)) {
                $tdClass = '';
                if ($display_privacy && $prevIsPublic == 0 && $row['access'] != Project::ACCESS_PRIVATE) {
                    $tdClass .= ' widget_my_projects_first_public';
                }

                $html .= '<tr class="'.util_get_alt_row_color($i++).'" >';

                // Privacy
                if ($display_privacy) {
                    if ($row['access'] === Project::ACCESS_PRIVATE) {
                        $privacy = 'icon-lock fa fa-lock';
                    } else {
                        $privacy = 'icon-unlock fa fa-unlock';
                    }
                    $html .= '<td class="widget_my_projects_privacy'.$tdClass.'"><i class="'.$privacy.' dashboard-widget-my-projects-icons"></i></td>';
                }

                // Project name
                $html .= '<td class="widget_my_projects_project_name'.$tdClass.'"><a href="/projects/'.$row['unix_group_name'].'/">'.$row['group_name'].'</a></td>';

                // Admin link
                $html .= '<td class="widget_my_projects_actions'.$tdClass.'">';
                if ($row['admin_flags'] == 'A') {
                    $html .= '<a href="/project/admin/?group_id='.$row['group_id'].'">['.$GLOBALS['Language']->getText('my_index', 'admin_link').']</a>';
                } else {
                    $html .= '&nbsp;';
                }
                $html .= '</td>';

                // Mailing tool
                $html .= '<td class="'.$tdClass.'">';
                $html .= '<a class="massmail-project-member-link" href="#massmail-project-members" data-project-id="'.$row['group_id'].'" title="'.$GLOBALS['Language']->getText('my_index','send_mail',$row['group_name']).'" data-toggle="modal"><span class="icon-envelope-alt fa fa-envelope-o"></span></a>';
                $html .= '</td>';

                // Remove from project
                $html .= '<td class="widget_my_projects_remove'.$tdClass.'">';
                if ($row['admin_flags'] != 'A') {
                    $html .= html_trash_link_fontawesome('rmproject.php?group_id='.$row['group_id'], $GLOBALS['Language']->getText('my_index', 'quit_proj'));
                } else {
                    $html .= '&nbsp;';
                }
                $html .= '</td>';

                $html .= '</tr>';

                $prevIsPublic = ($row['access'] !== Project::ACCESS_PRIVATE);
            }

            if ($display_privacy) {
                // Legend
                $html .= '<tr>';
                $html .= '<td colspan="5" class="widget_my_projects_legend">';
                $html .= '<span class="widget_my_projects_legend_title dashboard-widget-my-projects-legend-title">'.$GLOBALS['Language']->getText('my_index', 'my_projects_legend').'</span>';
                $html .= '<span class="dashboard-widget-my-projects-legend-content"><i class="icon-lock fa fa-lock dashboard-widget-my-projects-icons"></i> '.$GLOBALS['Language']->getText('project_privacy', 'private').'</span> ';
                $html .= '<span class="dashboard-widget-my-projects-legend-content"><i class="icon-unlock fa fa-unlock dashboard-widget-my-projects-icons"></i> '.$GLOBALS['Language']->getText('project_privacy', 'public').'</span>';
                $html .= '</td>';
                $html .= '</tr>';
            }

            $html .= '</table>';

            $html .= $this->fetchMassMailForm($token);
        }

        return $html;
    }

    function hasRss() {
        return true;
    }
    function displayRss() {
        $rss = new RSS(array(
            'title'       => 'Codendi - MyProjects',
            'description' => 'My projects',
            'link'        => get_server_url(),
            'language'    => 'en-us',
            'copyright'   => 'Copyright Xerox',
            'pubDate'     => gmdate('D, d M Y G:i:s',time()).' GMT',
        ));
        $result = db_query("SELECT groups.group_name,"
            . "groups.group_id,"
            . "groups.unix_group_name,"
            . "groups.status,"
            . "groups.access,"
            . "user_group.admin_flags "
            . "FROM groups,user_group "
            . "WHERE groups.group_id=user_group.group_id "
            . "AND user_group.user_id='". db_ei(user_getid()) ."' "
            . "AND groups.status='A' ORDER BY group_name");
        $rows=db_numrows($result);
        if (!$result || $rows < 1) {
            $rss->addItem(array(
                'title'       => 'Error',
                'description' => $GLOBALS['Language']->getText('my_index', 'not_member') . db_error(),
                'link'        => get_server_url()
            ));
        } else {
            for ($i=0; $i<$rows; $i++) {
                $title = db_result($result,$i,'group_name');
                if ( db_result($result,$i,'access') == Project::ACCESS_PRIVATE ) {
                    $title .= ' (*)';
                }

                $desc = 'Project: '. get_server_url() .'/project/admin/?group_id='.db_result($result,$i,'group_id') ."<br />\n";
                if ( db_result($result,$i,'admin_flags') == 'A' ) {
                    $desc .= 'Admin: '. get_server_url() .'/project/admin/?group_id='.db_result($result,$i,'group_id');
                }

                $rss->addItem(array(
                    'title'       => $title,
                    'description' => $desc,
                    'link'        => get_server_url() .'/projects/'. db_result($result,$i,'unix_group_name')
                ));
            }
        }
        $rss->display();
    }
    function getDescription() {
        return $GLOBALS['Language']->getText('widget_description_my_projects','description');
    }

    private function fetchMassMailForm(CSRFSynchronizerToken $token) {
        $presenter = new MassmailFormPresenter(
            $token,
            $GLOBALS['Language']->getText('my_index', 'massmail_form_title'),
            'massmail_to_project_members.php'
        );

        $template_factory = TemplateRendererFactory::build();
        $renderer         = $template_factory->getRenderer($presenter->getTemplateDir());

        return $renderer->renderToString('contact-modal', $presenter);
    }
}
