import React, { useState, useEffect, useRef } from 'react';
import DateSelect from '../shared/DateSelect'
import { Pane, SearchInput, UnorderedList, ListItem } from 'evergreen-ui'
import axios from 'axios'
import { QueryToObject } from '../formatters'
import urls from '../config/urls'
import GroupSummaryToEntity from '../mappings/GroupSummaryToEntity' 

export default (props) => {

    const [phrases, set_phrases] = useState([]);
    const [data_sets, set_data_sets] = useState([]);
    const [input_value, set_input_value] = useState('');
    const [params, set_params] = useState({});

    const searchInput = useRef(null);

    useEffect(() => {
        getPhrases();
    }, [phrases, params]);

    const dateChange = (data) => {
        set_params(data);
    }

    const getPhrases = (query = {}) => {
        if (phrases.length <= 0) return false;
        const data = {...params};

        data.query = phrases[0];

        const queryString = Object.keys(data)
            .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(data[k]))
            .join('&');

        axios.get(`${urls.trendsSearchUrl}?${queryString}`)
            .then(res => {

                console.log(res);

                if (res.data.result === 'success') {
                    
                } else {
                    console.log(res.data);
                }
            })
            .catch(e => {
                console.log(e);
            })
    }

    const renderPhrases = () => {
        if (phrases.length < 0) return null;
        return (
            <UnorderedList>
                { phrases.map((ph, idx) => {
                    return (
                        <ListItem
                            key={idx} 
                            icon="tick-circle" 
                            iconColor="success">
                            {ph}
                        </ListItem>
                    )
                }) }
            </UnorderedList>
        )
    }

    const handleKeyPress = (e) => {
        if(e.charCode === 13) {
            let phrase = input_value.trim();
            if (phrase !== '') {
                const newPhrases = [...phrases];
                newPhrases.unshift(phrase);
                set_phrases(newPhrases);
            }
        }

        return false;
    }

    return (<div style={{ minHeight: '100vh' }}>

        <div style={{ zIndex: 2, position: 'relative' }} className="block">
            <Pane display="flex">
                <SearchInput
                    ref={searchInput}
                    onKeyPress={handleKeyPress}  
                    onChange={(e) => { set_input_value(e.target.value) }}
                    height={48}
                    value={input_value} 
                    marginRight={16} 
                    placeholder="Что ищем?" />
                <DateSelect
                    onChange={dateChange} />
            </Pane>
        </div>
        <div>
            {renderPhrases()}
        </div>

    </div>
    );
}