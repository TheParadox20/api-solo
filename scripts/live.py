
import requests

url = 'https://live.betika.com/v1/uo/matches'
params = {'page': 1}
count = 0

while True:
    response = requests.get(url, params=params)
    data = response.json()
    
    if not data.get('data'):
        break

    for match in data.get('data', []):
        home_team = match.get('home_team', 'N/A')
        away_team = match.get('away_team', 'N/A')
        current_score = match.get('current_score', 'N/A')
        match_time = match.get('match_time', 'N/A')
        competition_name = match.get('competition_name', 'N/A')
        nation = match.get('category', 'N/A')
        sport_name = match.get('sport_name', 'N/A')

        if match_time in ['0', 'N/A', None]:
            match_time = 'Time Unknown'

        if current_score == '-:-':
            if match_time == 'Time Unknown':
                match_status = 'Score Available but Time Unknown'
            else:
                match_status = 'Score Unavailable'
        else:
        
            if match_time == 'Time Unknown':
                match_status = 'Score Available but Time Unknown'
            else:
                match_status = 'Live'
                
        count +=1

        # info
        print(f"Sport: {sport_name}")
        print(f"Match: {home_team} vs {away_team}")
        print(f"Score: {current_score}")
        print(f"Time: {match_time}")
        print(f"Competition: {competition_name}")
        print(f"Nation: {nation}")
        print(f"Status: {match_status}")
        print("-" * 50)
    
    params['page'] += 1
    
print(f"matches fetched: {count}")