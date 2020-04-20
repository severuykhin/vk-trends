import React from 'react'
import { Table, Badge, Pill } from 'evergreen-ui'

export default class Users extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {

        const { users, withKeywords } = this.props;

        return (
            <Table>
                <Table.Head>
                    <Table.TextHeaderCell 
                        flexShrink={0} 
                        flexGrow={0}
                        flexBasis={120}>
                        ID вк
                    </Table.TextHeaderCell>
                    <Table.TextHeaderCell
                        flexShrink={0} 
                        flexGrow={0}
                        flexBasis={180}>
                        Количество комментариев
                    </Table.TextHeaderCell>
                    {withKeywords && <Table.TextHeaderCell>
                        Словесный портрет
                    </Table.TextHeaderCell>}
                    <Table.TextHeaderCell>
                        Действия
                    </Table.TextHeaderCell>
                </Table.Head>
                <Table.Body height={240}>
                    {users.map(profile => (
                        <Table.Row height="auto" key={profile.key} isSelectable onSelect={() => {}}>
                            <Table.TextCell 
                                flexShrink={0} 
                                flexGrow={0}
                                flexBasis={120}>
                                    {profile.vk_user_id}
                            </Table.TextCell>
                            <Table.TextCell
                                flexShrink={0} 
                                flexGrow={0}
                                flexBasis={180}>
                                {profile.value}
                            </Table.TextCell>
                            {withKeywords && profile.keywords &&
                                <Table.Cell style={{ flexWrap: 'wrap', paddingTop: '4px' }}>{this.renderUserKeywords(profile.keywords)}</Table.Cell>
                            }
                            <Table.TextCell>{profile.value}</Table.TextCell>
                        </Table.Row>
                    ))}
                </Table.Body>
            </Table>
        )
    }

    renderUserKeywords(keywords) {
        if (!keywords.length) return null;

        return keywords.map(k => {
            return (
                <Badge
                    style={{
                        margin: '0px 4px 4px 0'
                    }}
                    textTransform="lowercase" color="neutral">
                    <span>{k.value}</span>
                    {/* <Pill display="inline-flex" margin={8}>0</Pill>  */}
                </Badge>
            );
        });
    }
}