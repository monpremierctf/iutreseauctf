pid="$(ps e | grep 'python3 ctf-monitor.py' | grep -v 'grep' | cut -d' ' -f2)"
echo "pid=[$pid]"
if [ ! -z "$pid" ]; then
    echo "Killing $pid"
    kill -9 $pid 
fi