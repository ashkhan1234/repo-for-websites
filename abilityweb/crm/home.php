<link rel="stylesheet" href="/abilityweb/crm/charting/chartist.min.css" > <!-- To remain separate -->
<script src="/abilityweb/crm/charting/chartist.min.js"></script> <!-- To remain separate -->
<link rel="stylesheet" href="/abilityweb/crm/charting/user-home.css" > <!-- To remain separate -->
<script src="/abilityweb/crm/charting/user-home.js"></script> <!-- To remain separate -->

<div class="home-page-scroll-wrapper">
    <div class="allow-vertical-scroll-only">


        <div id="crm-user-charts">


            <div class="user-chart-outer flex-xs-hidden flex-xs-1 flex-sm-2 flex-md-2 flex-lg-1 no-padding user">
                <div class="pic">
                    <div id="h-user-img" class="square-center-background-image"></div>
                </div>
            </div>



            <div class="user-chart-outer flex-xs-8 flex-sm-6 flex-md-6 flex-lg-3 block-with-title ranking-box userHomeLoading userStatsLoading active">
                <div class="sub-block-title insert_user_name"></div>
                <div class="block-title"><? echo date('M Y'); ?> Sales Rank</div>

                <div class="ranking-text">
                    <div class="flex-box chart-mini-nav">
                        <div class="mini-nav-btn active" data-parent="home" data-target="home-user-sales-ranking" data-val="vehicle_sales_rank_my_location">My Location</div>
                        <div class="mini-nav-btn" data-parent="home" data-target="home-user-sales-ranking" data-val="vehicle_sales_rank_all">All Locations</div>
                    </div>
                    <div class="stat-details"></div>
                </div>
                <div class="ranking-medal-wrapper">
                    <img  class="ranking-medal" src="/abilityweb/crm/img/ranking-medal.svg">
                    <div class="ranking-pos-number">2<span class="thndrd">nd</span></div>
                </div>
            </div>




            <div class="user-chart-outer flex-xs-8 flex-sm-4 flex-md-5 flex-lg-2 block-with-title userHomeLoading userStatsLoading active">
                <div class="square-pie-chart">
                    <div class="square-pie-chart-upper">
                        <div class="water-level-wrapper">
                            <div class="water-slosh-wrapper">
                                <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 340 209"> <defs> <style> .cls-1 { fill: url(#linear-gradient); } </style> <linearGradient id="linear-gradient" x1="170" y1="4.09" x2="170" y2="40" gradientUnits="userSpaceOnUse"> <stop offset="0" stop-color="#6fc8ee"/> <stop offset="1" stop-color="#0a56a6"/> </linearGradient> </defs> <title>wave</title> <path class="cls-1" d="M170,2.57C113.33-4.15,56.67,9.29,0,2.57V209.37H340V2.57C283.33-4.15,226.67,9.29,170,2.57Z"/> </svg>
                            </div>
                        </div>
                        <div class="block-title">...</div>
                    </div>
                    <div class="square-pie-chart-lower">
                        <div class="block-title">...</div>
                    </div>
                    <img class="scale-graph" src="/abilityweb/crm/charting/vertical-scale.svg">
                </div>
            </div>



            <div id="vehicle-sales-leader-block" class="user-chart-outer flex-xs-8 flex-sm-4 flex-md-3 flex-lg-2 block-with-title sales-leader-this-month userHomeLoading userStatsLoading active">

            </div>





            <div class="user-chart-outer flex-xs-8 flex-sm-8 flex-md-8 flex-lg-8 block-with-title userHomeLoading userStatsLoading active">
                <div id="home-user-sales-timeback">
                <div class="block-title"><span class="insert_user_name"></span> Performance</div>
                <div class="flex-box chart-mini-nav">
                    <div class="mini-nav-btn" data-parent="home" data-target="home-user-sales-timeback" data-val="12">1 Year</div>
                    <div class="mini-nav-btn" data-parent="home" data-target="home-user-sales-timeback" data-val="6">6 Months</div>
                    <div class="mini-nav-btn" data-parent="home" data-target="home-user-sales-timeback" data-val="3">3 Months</div>
                    <div class="mini-nav-btn" data-parent="home" data-target="home-user-sales-timeback" data-val="1">30 Days</div>
                </div>
                </div>
                <div class="flex-box nested">
                    <div class="flex-xs-8 flex-sm-8 flex-md-8 flex-lg-4" style="padding-bottom: 10px;">
                        <div class="block-title">Vehicle Sales History</div>
                        <div id="user-sales-line" class="user-chart"></div>
                    </div>
                    <div class="flex-xs-8 flex-sm-4 flex-md-4 flex-lg-2">
                        <div class="block-title text-center">Overall Closing Rate</div>
                        <div data-target="closing_rate_overall" class="chart-gauge high-is-best">
                            <div class="gauge-bg">
                                <div class="gauge-needle"></div>
                                <div class="amount-title">...</div>
                            </div>
                            <div class="gauge-averages"></div>
                        </div>
                    </div>

                    <div class="flex-xs-8 flex-sm-4 flex-md-4 flex-lg-2">
                        <div class="block-title text-center">E-Leads Closing Rate</div>
                        <div data-target="closing_rate_eleads" class="chart-gauge high-is-best">
                            <div class="gauge-bg">
                                <div class="gauge-needle"></div>
                                <div class="amount-title">...</div>
                            </div>
                            <div class="gauge-averages"></div>
                        </div>

                    </div>

                </div>




            </div>





            <div class="user-chart-outer flex-xs-8 flex-sm-8 flex-md-8 flex-lg-4 block-with-title userHomeLoading userRecentFormsLoading">
                <div class="top-right-tools">
                    <a href="javascript: $('.recent_forms-big-btn').trigger('click')">More <i class="moon moon-arrow-right16"></i></a>
                </div>
                <div class="block-title">Recent Activity</div>
                <div id="home-user-salesleads"></div>
            </div>



            <div class="user-chart-outer flex-xs-8 flex-sm-8 flex-md-8 flex-lg-4 block-with-title userHomeLoading userRecommendedFormsLoading">
                <div class="top-right-tools">
                </div>
                <div class="block-title">Recommended Leads To Follow Up</div>
                <div id="home-user-recommended"></div>
            </div>


            <div class="user-chart-outer flex-xs-8 flex-sm-8 flex-md-8 flex-lg-8 block-with-title userHomeLoading userStatsLoading active">
                <div class="top-right-tools">
                </div>
                <div class="block-title">My Vehicle Holds</div>
                <div id="home-user-my-vehicle-holds"></div>
            </div>



        </div>
    </div>
</div>
<!-- END Changes 4/26/17 -->

