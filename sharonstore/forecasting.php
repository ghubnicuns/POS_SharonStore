<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forecasting & Restocking – Sharon Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon-sm">🛒</div>
        <span class="logo-text">Sharon<span class="logo-accent">Store</span></span>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item" id="nav-dashboard"><i class="fa fa-house"></i><span>Dashboard</span></a>
        <a href="pos.php"       class="nav-item" id="nav-pos"><i class="fa fa-cash-register"></i><span>Point of Sale</span></a>
        <a href="inventory.php" class="nav-item" id="nav-inventory"><i class="fa fa-boxes-stacked"></i><span>Inventory</span></a>
        <a href="forecasting.php" class="nav-item" id="nav-forecasting"><i class="fa fa-chart-line"></i><span>Forecasting & Restock</span></a>
    </nav>
        <div class="sidebar-footer">
            <div class="user-pill">
                <div class="user-avatar" id="sidebarAvatar">A</div>
                <div class="user-info">
                    <div class="user-name" id="sidebarName">Admin</div>
                    <div class="user-role" id="sidebarRole">Administrator</div>
                </div>
            </div>
            <button class="logout-btn" id="logoutBtn" title="Sign Out">
                <i class="fa fa-right-from-bracket"></i>
            </button>
        </div>
</aside>

<main class="main-content">
    <header class="topbar">
        <button class="menu-toggle" id="menuToggle"><i class="fa fa-bars"></i></button>
        <div class="topbar-title">Sales Forecasting & Restocking</div>
        <div class="topbar-right"><span class="topbar-date" id="topbarDate"></span></div>
    </header>

    <div class="page-body">
        <!-- KPI Row -->
        <div class="stats-grid" style="margin-bottom:20px;">
            <div class="stat-card" style="--accent:#e91e8c">
                <div class="stat-icon"><i class="fa fa-chart-line"></i></div>
                <div class="stat-info"><div class="stat-value" id="kpiAvgDaily">₱0</div><div class="stat-label">Avg Daily Revenue (30d)</div></div>
            </div>
            <div class="stat-card" style="--accent:#7c3aed">
                <div class="stat-icon"><i class="fa fa-arrow-trend-up"></i></div>
                <div class="stat-info"><div class="stat-value" id="kpiForecast7">₱0</div><div class="stat-label">Forecasted Revenue (7d)</div></div>
            </div>
            <div class="stat-card" style="--accent:#f59e0b">
                <div class="stat-icon"><i class="fa fa-rotate"></i></div>
                <div class="stat-info"><div class="stat-value" id="kpiRestock">0</div><div class="stat-label">Items Need Restocking</div></div>
            </div>
            <div class="stat-card" style="--accent:#22c55e">
                <div class="stat-icon"><i class="fa fa-calendar-check"></i></div>
                <div class="stat-info"><div class="stat-value" id="kpiBestDay">—</div><div class="stat-label">Best Sales Day (30d)</div></div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" data-tab="forecasting">📈 Sales Forecasting</button>
            <button class="tab-btn" data-tab="restocking">📦 Restocking</button>
            <button class="tab-btn" data-tab="insights">💡 Insights</button>
        </div>

        <!-- ===== TAB: FORECASTING ===== -->
        <div class="tab-panel active" id="tab-forecasting">
            <div class="insight-box" id="forecastInsight">
                📊 Loading forecast analysis…
            </div>
            <div class="fc-grid">
                <div class="chart-card">
                    <div class="chart-title">Daily Revenue – Last 30 Days</div>
                    <div class="chart-sub">Actual revenue vs 7-day moving average</div>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">7-Day Revenue Forecast</div>
                    <div class="chart-sub">Projected based on recent trend (linear regression)</div>
                    <canvas id="forecastChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Revenue by Day of Week</div>
                <div class="chart-sub">Average revenue per weekday (30-day window)</div>
                <canvas id="weekdayChart" style="max-height:200px;"></canvas>
            </div>

            <!-- Forecast Table -->
            <div class="section-header" style="margin-top:24px;">
                <div>
                    <div class="section-title">7-Day Revenue Forecast</div>
                    <div class="section-sub">Predicted daily revenue for the next 7 days</div>
                </div>
            </div>
            <div class="table-wrap">
                <table class="data-table forecast-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Forecast Revenue</th>
                            <th>Lower Bound</th>
                            <th>Upper Bound</th>
                            <th>Confidence</th>
                        </tr>
                    </thead>
                    <tbody id="forecastTableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- ===== TAB: RESTOCKING ===== -->
        <div class="tab-panel" id="tab-restocking">
            <div class="page-controls">
                <div class="search-wrap">
                    <i class="fa fa-magnifying-glass"></i>
                    <input type="text" class="search-input" id="restockSearch" placeholder="Search items…">
                </div>
                <select class="filter-select" id="restockFilter">
                    <option value="all">All Items</option>
                    <option value="critical">Critical (Out of Stock)</option>
                    <option value="low">Low Stock</option>
                    <option value="ok">Well Stocked</option>
                </select>
                <button class="btn btn-pink" id="printRestockBtn" style="margin-left:auto;"><i class="fa fa-print"></i> Print List</button>
            </div>

            <div class="restock-grid" id="restockGrid"></div>
        </div>

        <!-- ===== TAB: INSIGHTS ===== -->
        <div class="tab-panel" id="tab-insights">
            <div class="fc-grid">
                <div class="chart-card">
                    <div class="chart-title">Top Selling Categories</div>
                    <div class="chart-sub">By revenue share (estimated)</div>
                    <canvas id="categoryChart" style="max-height:260px;"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">Stock Health Overview</div>
                    <div class="chart-sub">In Stock vs Low Stock vs Out of Stock</div>
                    <canvas id="stockHealthChart" style="max-height:260px;"></canvas>
                </div>
            </div>
            <div id="insightCards" class="restock-grid"></div>
        </div>
    </div>
</main>

<script src="app.js"></script>
<script>
checkAuth();
requireRole(['Admin', 'Manager']);
initNav('nav-forecasting');
document.getElementById('topbarDate').textContent = new Date().toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

// ===== DATA =====
const txs = getTransactions();
const inv = getInventory();
const today = new Date();

// Build daily revenue map (last 30 days)
const days30 = [];
const dailyRevMap = {};
for(let i=29;i>=0;i--){
    const d = new Date(today); d.setDate(d.getDate()-i);
    const key = d.toDateString();
    days30.push(d);
    dailyRevMap[key] = 0;
}
txs.forEach(t => {
    const key = new Date(t.date).toDateString();
    if(dailyRevMap[key] !== undefined) dailyRevMap[key] += t.total;
});
const dailyRevArr = days30.map(d => dailyRevMap[d.toDateString()]);
const labels30 = days30.map(d => d.toLocaleDateString('en-PH',{month:'short',day:'numeric'}));

// 7-day moving average
function movingAvg(arr, w) {
    return arr.map((_, i) => {
        if(i < w-1) return null;
        const slice = arr.slice(i-w+1, i+1);
        return slice.reduce((a,b)=>a+b,0)/w;
    });
}
const ma7 = movingAvg(dailyRevArr, 7);

// Linear regression for forecast
function linearRegression(vals) {
    const n = vals.length;
    const xMean = (n-1)/2;
    const yMean = vals.reduce((a,b)=>a+b,0)/n;
    let num=0, den=0;
    vals.forEach((y,x) => { num+=(x-xMean)*(y-yMean); den+=(x-xMean)**2; });
    const slope = den ? num/den : 0;
    const intercept = yMean - slope*xMean;
    return {slope, intercept};
}
const {slope, intercept} = linearRegression(dailyRevArr);
const forecast7 = Array.from({length:7},(_,i)=>Math.max(0, intercept + slope*(30+i)));
const fLabels = Array.from({length:7},(_,i)=>{
    const d=new Date(today); d.setDate(d.getDate()+i+1);
    return d.toLocaleDateString('en-PH',{month:'short',day:'numeric'});
});
const fDates = Array.from({length:7},(_,i)=>{ const d=new Date(today); d.setDate(d.getDate()+i+1); return d; });

// Weekday averages
const wdSums=[0,0,0,0,0,0,0], wdCounts=[0,0,0,0,0,0,0];
days30.forEach((d,i)=>{ const wd=d.getDay(); wdSums[wd]+=dailyRevArr[i]; wdCounts[wd]++; });
const wdAvg = wdSums.map((s,i)=> wdCounts[i]?s/wdCounts[i]:0);
const wdNames=['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

// KPIs
const avgDaily = dailyRevArr.reduce((a,b)=>a+b,0)/30;
document.getElementById('kpiAvgDaily').textContent = '₱'+avgDaily.toFixed(0);
document.getElementById('kpiForecast7').textContent = '₱'+forecast7.reduce((a,b)=>a+b,0).toFixed(0);
const restockCount = inv.filter(i=>i.stock<=(i.reorderLevel||20)).length;
document.getElementById('kpiRestock').textContent = restockCount;
const bestIdx = dailyRevArr.indexOf(Math.max(...dailyRevArr));
document.getElementById('kpiBestDay').textContent = days30[bestIdx]?.toLocaleDateString('en-PH',{month:'short',day:'numeric'})||'—';

// Forecast insight
const trendDir = slope > 0 ? '📈 upward' : '📉 downward';
const trendPct = avgDaily ? Math.abs(slope*7/avgDaily*100).toFixed(1) : 0;
document.getElementById('forecastInsight').innerHTML =
    `Your store shows a <strong>${trendDir} trend</strong> of <strong>₱${slope.toFixed(0)}/day</strong>. `+
    `Forecasted revenue for the next 7 days is <strong>₱${forecast7.reduce((a,b)=>a+b,0).toFixed(2)}</strong> `+
    `(${trendPct}% ${slope>=0?'above':'below'} current 30-day average). `+
    `${restockCount > 0 ? `⚠️ <strong>${restockCount} item(s)</strong> need restocking soon.` : '✅ All inventory levels are healthy.'}`;

// CHARTS
const chartDefaults = {
    color: '#a78bca',
    borderColor: 'rgba(255,255,255,0.08)',
    plugins: { legend:{ labels:{ color:'#a78bca', font:{family:'Inter',size:12} } } },
    scales: {
        x:{ ticks:{color:'#7c6d94',font:{size:11}}, grid:{color:'rgba(255,255,255,0.05)'} },
        y:{ ticks:{color:'#7c6d94',font:{size:11},callback:v=>'₱'+v.toLocaleString()}, grid:{color:'rgba(255,255,255,0.05)'} }
    }
};

// Revenue chart
new Chart(document.getElementById('revenueChart'), {
    type:'line',
    data:{
        labels: labels30,
        datasets:[
            { label:'Daily Revenue', data:dailyRevArr, borderColor:'#e91e8c', backgroundColor:'rgba(233,30,140,0.08)', tension:.4, pointRadius:2, borderWidth:2 },
            { label:'7-Day MA', data:ma7, borderColor:'#7c3aed', backgroundColor:'transparent', tension:.4, pointRadius:0, borderWidth:2, borderDash:[6,3] }
        ]
    },
    options:{ responsive:true, ...chartDefaults }
});

// Forecast chart
new Chart(document.getElementById('forecastChart'), {
    type:'bar',
    data:{
        labels: fLabels,
        datasets:[
            { label:'Forecast', data:forecast7, backgroundColor:'rgba(124,58,237,0.5)', borderColor:'#7c3aed', borderWidth:2, borderRadius:6 },
            { label:'Upper Bound', data:forecast7.map(v=>v*1.15), type:'line', borderColor:'rgba(34,197,94,0.5)', backgroundColor:'transparent', tension:.4, pointRadius:0, borderDash:[5,3] },
            { label:'Lower Bound', data:forecast7.map(v=>v*0.85), type:'line', borderColor:'rgba(239,68,68,0.5)', backgroundColor:'transparent', tension:.4, pointRadius:0, borderDash:[5,3] }
        ]
    },
    options:{ responsive:true, ...chartDefaults }
});

// Weekday chart
new Chart(document.getElementById('weekdayChart'), {
    type:'bar',
    data:{
        labels: wdNames,
        datasets:[{ label:'Avg Revenue', data:wdAvg, backgroundColor: wdAvg.map(v=>v===Math.max(...wdAvg)?'rgba(233,30,140,0.7)':'rgba(124,58,237,0.4)'), borderRadius:8 }]
    },
    options:{ responsive:true, ...chartDefaults }
});

// Forecast table
const ftb = document.getElementById('forecastTableBody');
const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
ftb.innerHTML = fDates.map((d,i)=>{
    const f = forecast7[i]; const lo = f*.85; const hi = f*1.15;
    const conf = Math.max(60, Math.round(90 - Math.abs(slope)*i/avgDaily*10)) || 80;
    return `<tr>
        <td>${d.toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}</td>
        <td>${days[d.getDay()]}</td>
        <td style="color:var(--pink-light);font-weight:700;">₱${f.toFixed(2)}</td>
        <td style="color:var(--danger);">₱${lo.toFixed(2)}</td>
        <td style="color:var(--success);">₱${hi.toFixed(2)}</td>
        <td><span class="${conf>=80?'badge-success':conf>=65?'badge-warn':'badge-danger'}">${conf}%</span></td>
    </tr>`;
}).join('');

// ===== RESTOCKING =====
function getStockPct(item){ return Math.min(100, Math.round(item.stock/(Math.max(item.reorderLevel||20,1)*3)*100)); }
function getStockClass(item){ if(item.stock===0) return 'danger'; if(item.stock<=(item.reorderLevel||20)) return 'warn'; return 'success'; }
function getBarColor(item){ return {danger:'#ef4444',warn:'#f59e0b',success:'#22c55e'}[getStockClass(item)]; }
function suggestOrder(item){ return Math.max(0,(item.reorderLevel||20)*4 - item.stock); }

function renderRestockGrid(){
    const q = document.getElementById('restockSearch').value.toLowerCase();
    const f = document.getElementById('restockFilter').value;
    let items = inv.filter(item=>{
        const matchQ = item.name.toLowerCase().includes(q)||item.category.toLowerCase().includes(q);
        const st = item.stock===0?'critical':item.stock<=(item.reorderLevel||20)?'low':'ok';
        const matchF = f==='all'||(f==='critical'&&st==='critical')||(f==='low'&&st==='low')||(f==='ok'&&st==='ok');
        return matchQ && matchF;
    });
    // Sort: critical first, then low, then ok
    items.sort((a,b)=>{ const order={out:0,warn:1,ok:2}; return (a.stock===0?0:a.stock<=(a.reorderLevel||20)?1:2)-(b.stock===0?0:b.stock<=(b.reorderLevel||20)?1:2); });
    const grid = document.getElementById('restockGrid');
    if(!items.length){ grid.innerHTML='<div class="empty-state" style="grid-column:1/-1;padding:48px;"><i class="fa fa-check-circle fa-2x" style="color:var(--success);margin-bottom:8px;display:block;"></i><p>No items match your filter.</p></div>'; return; }
    const cls = getStockClass;
    grid.innerHTML = items.map(item=>{
        const pct = getStockPct(item); const color = getBarColor(item);
        const suggest = suggestOrder(item); const sc = cls(item);
        const badge = sc==='danger'?'badge-danger':sc==='warn'?'badge-warn':'badge-success';
        const label = sc==='danger'?'Out of Stock':sc==='warn'?'Low Stock':'In Stock';
        return `<div class="restock-card">
            <div class="rc-head">
                <div><div class="rc-name">${item.name}</div><div class="rc-cat">${item.category}</div></div>
                <span class="${badge}" style="font-size:11px;padding:3px 9px;border-radius:20px;border:1px solid;white-space:nowrap;">${label}</span>
            </div>
            <div class="bar-wrap">
                <div class="bar-labels"><span>Current: <strong style="color:${color};">${item.stock} ${item.unit}</strong></span><span>Reorder @ ${item.reorderLevel||20}</span></div>
                <div class="bar-track"><div class="bar-fill" style="width:${pct}%;background:${color};"></div></div>
            </div>
            <div class="rc-foot">
                <div class="rc-meta">Price: ₱${item.price.toFixed(2)} / ${item.unit}</div>
                ${suggest>0?`<span class="badge-warn" style="font-size:11px;padding:3px 9px;border-radius:20px;border:1px solid rgba(245,158,11,.3);">Order ~${suggest} ${item.unit}</span>`:'<span class="badge-success" style="font-size:11px;padding:3px 9px;border-radius:20px;border:1px solid rgba(34,197,94,.3);">Stocked</span>'}
            </div>
        </div>`;
    }).join('');
}

document.getElementById('restockSearch').addEventListener('input', renderRestockGrid);
document.getElementById('restockFilter').addEventListener('change', renderRestockGrid);
document.getElementById('printRestockBtn').addEventListener('click', ()=>window.print());
renderRestockGrid();

// ===== INSIGHTS CHARTS =====
const catGroups = {};
inv.forEach(i=>{ catGroups[i.category]=(catGroups[i.category]||0)+i.price*i.stock; });
new Chart(document.getElementById('categoryChart'),{
    type:'doughnut',
    data:{
        labels:Object.keys(catGroups),
        datasets:[{data:Object.values(catGroups),backgroundColor:['rgba(233,30,140,0.7)','rgba(124,58,237,0.7)','rgba(14,165,233,0.7)','rgba(34,197,94,0.7)'],borderWidth:0,hoverOffset:8}]
    },
    options:{responsive:true,plugins:{legend:{labels:{color:'#a78bca',font:{family:'Inter',size:12}}}}}
});

const ok=inv.filter(i=>i.stock>(i.reorderLevel||20)).length;
const low=inv.filter(i=>i.stock>0&&i.stock<=(i.reorderLevel||20)).length;
const out=inv.filter(i=>i.stock===0).length;
new Chart(document.getElementById('stockHealthChart'),{
    type:'doughnut',
    data:{
        labels:['In Stock','Low Stock','Out of Stock'],
        datasets:[{data:[ok,low,out],backgroundColor:['rgba(34,197,94,0.7)','rgba(245,158,11,0.7)','rgba(239,68,68,0.7)'],borderWidth:0,hoverOffset:8}]
    },
    options:{responsive:true,plugins:{legend:{labels:{color:'#a78bca',font:{family:'Inter',size:12}}}}}
});

// Insight cards
const insights = [
    { icon:'🏆', title:'Best Revenue Day', text:`Your highest revenue day in the last 30 days was <strong>₱${Math.max(...dailyRevArr).toFixed(2)}</strong> on ${days30[bestIdx].toLocaleDateString('en-PH',{weekday:'long',month:'long',day:'numeric'})}.` },
    { icon:'📦', title:'Restock Alert', text:`<strong>${restockCount} product(s)</strong> are at or below reorder level. Immediate restocking is recommended to prevent stockouts.` },
    { icon:'📅', title:'Best Weekday', text:`<strong>${wdNames[wdAvg.indexOf(Math.max(...wdAvg))]}</strong> generates the highest average revenue of <strong>₱${Math.max(...wdAvg).toFixed(2)}</strong> per day.` },
    { icon:'📈', title:'Growth Trend', text:`Your daily revenue trend is <strong>${slope>=0?'+':''}₱${slope.toFixed(2)}/day</strong>. At this rate, monthly revenue projection is <strong>₱${(avgDaily*30+slope*15*30).toFixed(2)}</strong>.` },
];
document.getElementById('insightCards').innerHTML = insights.map(ins=>`
    <div class="restock-card">
        <div style="font-size:28px;margin-bottom:10px;">${ins.icon}</div>
        <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:8px;">${ins.title}</div>
        <div style="font-size:13px;color:var(--text2);line-height:1.6;">${ins.text}</div>
    </div>`).join('');

// Tabs
document.querySelectorAll('.tab-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
        document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-'+btn.dataset.tab).classList.add('active');
    });
});
</script>
</body>
</html>
