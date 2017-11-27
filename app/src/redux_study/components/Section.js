import React from 'react'
import PropTyps from 'prop-types'

const Section = ({id, name}) => (
    <li id="{id}">{name} <input type="radio" name="selectedSection"/></li>
)

export default Section;